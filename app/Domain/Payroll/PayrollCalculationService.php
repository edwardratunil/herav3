<?php

namespace App\Domain\Payroll;

use App\Domain\Academic\FacultyLoadCalculator;
use App\Domain\Attendance\AttendanceDtrEngine;
use App\Domain\Payroll\Calculators\BirWithholdingTaxCalculator;
use App\Domain\Payroll\Calculators\GsisCalculator;
use App\Domain\Payroll\Calculators\PagIbigCalculator;
use App\Domain\Payroll\Calculators\PhilHealthCalculator;
use App\Domain\Payroll\Calculators\SssCalculator;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\PayrollItemBreakdown;
use App\Models\PayrollRun;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayrollCalculationService
{
    public const SCALE = 4;

    public function __construct(
        protected SssCalculator $sssCalculator,
        protected PhilHealthCalculator $philHealthCalculator,
        protected PagIbigCalculator $pagIbigCalculator,
        protected GsisCalculator $gsisCalculator,
        protected BirWithholdingTaxCalculator $taxCalculator,
        protected FacultyLoadCalculator $facultyLoadCalculator,
        protected AttendanceDtrEngine $attendanceEngine
    ) {}

    /**
     * Calculate and record a payroll item for a single employee in a given run.
     */
    public function calculateEmployee(PayrollRun $run, Employee $employee): PayrollItem
    {
        if ($run->isLocked()) {
            throw new RuntimeException("Cannot calculate item for locked payroll run {$run->id}");
        }

        $contract = $employee->activeContract;
        if (!$contract) {
            throw new RuntimeException("Active contract missing for employee {$employee->id}");
        }

        $institutionType = $run->tenant->institution_type ?? 'private_hei';
        $isPublicSector = in_array($institutionType, ['deped', 'suc', 'lgu']);

        // 1. Basic Pay Computation
        $baseRate = $contract->base_rate;
        if ($run->payout_frequency === 'semi_monthly') {
            $basicEarned = ($contract->salary_basis === 'monthly')
                ? bcdiv($baseRate, '2.0000', self::SCALE)
                : $baseRate;
            $monthlyEquivalent = ($contract->salary_basis === 'monthly')
                ? $baseRate
                : bcmul($baseRate, '2.0000', self::SCALE);
        } else {
            $basicEarned = $baseRate;
            $monthlyEquivalent = $baseRate;
        }

        // 2. Overload & Faculty Load Pay
        $overloadPay = '0.0000';
        if ($employee->is_faculty) {
            $facultyLoads = $employee->facultyLoads()
                ->where('is_approved_by_dean', true)
                ->get();

            $totalLecture = '0.0000';
            $totalLab = '0.0000';
            foreach ($facultyLoads as $fl) {
                $totalLecture = bcadd($totalLecture, (string)$fl->lecture_units, self::SCALE);
                $totalLab = bcadd($totalLab, (string)$fl->lab_contact_hours, self::SCALE);
            }

            if (bccomp($totalLecture, '0.0000', self::SCALE) > 0 || bccomp($totalLab, '0.0000', self::SCALE) > 0) {
                $overloadResult = $this->facultyLoadCalculator->calculateOverload(
                    $totalLecture,
                    $totalLab,
                    '0.7500',
                    '18.0000',
                    '850.0000'
                );
                $overloadPay = $overloadResult['overload_pay'];
            }
        }

        // 3. Allowances
        $taxableAllowance = $contract->taxable_allowance ?? '0.0000';
        $deMinimis = $contract->de_minimis_allowance ?? '0.0000';

        // 4. Gross Pay
        $grossPay = bcadd(bcadd(bcadd($basicEarned, $overloadPay, self::SCALE), $taxableAllowance, self::SCALE), $deMinimis, self::SCALE);

        // 5. Statutory Deductions
        $sssEe = '0.0000';
        $sssEr = '0.0000';
        $ecEr = '0.0000';
        $gsisEe = '0.0000';
        $gsisEr = '0.0000';

        if ($isPublicSector) {
            $gsisRes = ($run->payout_frequency === 'semi_monthly')
                ? $this->gsisCalculator->calculateSemiMonthly($monthlyEquivalent)
                : $this->gsisCalculator->calculateMonthly($monthlyEquivalent);
            $gsisEe = $gsisRes['employee_share'];
            $gsisEr = $gsisRes['employer_share'];
        } else {
            $sssRes = ($run->payout_frequency === 'semi_monthly')
                ? $this->sssCalculator->calculateSemiMonthly($monthlyEquivalent)
                : $this->sssCalculator->calculateMonthly($monthlyEquivalent);
            $sssEe = $sssRes['total_ee'];
            $sssEr = $sssRes['total_er'];
        }

        $phRes = ($run->payout_frequency === 'semi_monthly')
            ? $this->philHealthCalculator->calculateSemiMonthly($monthlyEquivalent)
            : $this->philHealthCalculator->calculateMonthly($monthlyEquivalent);
        $phEe = $phRes['employee_share'];
        $phEr = $phRes['employer_share'];

        $hdmfRes = ($run->payout_frequency === 'semi_monthly')
            ? $this->pagIbigCalculator->calculateSemiMonthly($monthlyEquivalent)
            : $this->pagIbigCalculator->calculateMonthly($monthlyEquivalent);
        $hdmfEe = $hdmfRes['employee_share'];
        $hdmfEr = $hdmfRes['employer_share'];

        // Total Statutory EE Deductions
        $totalStatutoryEe = bcadd(bcadd(bcadd($sssEe, $gsisEe, self::SCALE), $phEe, self::SCALE), $hdmfEe, self::SCALE);

        // 6. Attendance & DTR Deductions (Tardiness, Undertime, Unpaid Absences)
        $logs = $employee->attendanceLogs()
            ->whereBetween('log_date', [$run->period_start->toDateString(), $run->period_end->toDateString()])
            ->get();

        $totalTardinessMin = (int) $logs->sum('tardiness_minutes');
        $totalUndertimeMin = (int) $logs->sum('undertime_minutes');
        $unpaidAbsenceDays = '0.0000';
        foreach ($logs as $log) {
            if ($log->status === 'absent') {
                $unpaidAbsenceDays = bcadd($unpaidAbsenceDays, '1.0000', self::SCALE);
            } elseif ($log->status === 'half_day') {
                $unpaidAbsenceDays = bcadd($unpaidAbsenceDays, '0.5000', self::SCALE);
            }
        }

        $factorDays = $isPublicSector ? AttendanceDtrEngine::FACTOR_5_DAYS : AttendanceDtrEngine::FACTOR_6_DAYS;
        $attendanceDeductions = $this->attendanceEngine->calculateAttendanceDeduction(
            $monthlyEquivalent,
            $totalTardinessMin,
            $totalUndertimeMin,
            $unpaidAbsenceDays,
            $factorDays
        );

        $tardyUndertimeDeduction = bcadd($attendanceDeductions['tardiness_deduction'], $attendanceDeductions['undertime_deduction'], self::SCALE);
        $absenceDeduction = $attendanceDeductions['absence_deduction'];
        $totalAttendanceDeduction = $attendanceDeductions['total_attendance_deduction'];

        // 7. Taxable Income & Withholding Tax
        // Gross Taxable = Gross Pay - De Minimis - Total Attendance Deductions - Total Mandatory Statutory EE
        $grossTaxable = bcsub(bcsub(bcsub($grossPay, $deMinimis, self::SCALE), $totalAttendanceDeduction, self::SCALE), $totalStatutoryEe, self::SCALE);
        if (bccomp($grossTaxable, '0.0000', self::SCALE) < 0) {
            $grossTaxable = '0.0000';
        }

        $withholdingTax = ($run->payout_frequency === 'semi_monthly')
            ? $this->taxCalculator->calculateSemiMonthly($grossTaxable)
            : $this->taxCalculator->calculateMonthly($grossTaxable);

        // 8. Total Deductions & Net Pay
        $totalDeductions = bcadd(bcadd($totalStatutoryEe, $withholdingTax, self::SCALE), $totalAttendanceDeduction, self::SCALE);
        $netPay = bcsub($grossPay, $totalDeductions, self::SCALE);

        // GAA Floor check for public schools (₱5,000 threshold)
        if ($isPublicSector && bccomp($netPay, '5000.0000', self::SCALE) < 0) {
            // Flag in breakdown/audit snapshot
        }

        return DB::transaction(function () use (
            $run, $employee, $basicEarned, $overloadPay, $taxableAllowance, $deMinimis,
            $grossPay, $sssEe, $sssEr, $gsisEe, $gsisEr, $phEe, $phEr, $hdmfEe, $hdmfEr,
            $tardyUndertimeDeduction, $absenceDeduction, $totalAttendanceDeduction,
            $withholdingTax, $totalDeductions, $netPay, $isPublicSector, $logs
        ) {
            // Delete previous draft item if re-calculating
            PayrollItem::where('payroll_run_id', $run->id)
                ->where('employee_id', $employee->id)
                ->delete();

            $item = PayrollItem::create([
                'payroll_run_id'                => $run->id,
                'tenant_id'                     => $run->tenant_id,
                'employee_id'                   => $employee->id,
                'basic_earned'                  => $this->toInternalScale($basicEarned),
                'faculty_overload_pay'          => $this->toInternalScale($overloadPay),
                'allowances_taxable'            => $this->toInternalScale($taxableAllowance),
                'allowances_non_taxable'        => $this->toInternalScale($deMinimis),
                'gross_pay'                     => $this->toInternalScale($grossPay),
                'sss_ee'                        => $this->toInternalScale($sssEe),
                'sss_er'                        => $this->toInternalScale($sssEr),
                'gsis_ee'                       => $this->toInternalScale($gsisEe),
                'gsis_er'                       => $this->toInternalScale($gsisEr),
                'philhealth_ee'                 => $this->toInternalScale($phEe),
                'philhealth_er'                 => $this->toInternalScale($phEr),
                'pagibig_ee'                    => $this->toInternalScale($hdmfEe),
                'pagibig_er'                    => $this->toInternalScale($hdmfEr),
                'tardiness_undertime_deduction' => $this->toInternalScale($tardyUndertimeDeduction),
                'other_deductions'              => $this->toInternalScale($absenceDeduction),
                'withholding_tax'               => $this->toInternalScale($withholdingTax),
                'total_deductions'              => $this->toInternalScale($totalDeductions),
                'net_pay'                       => $this->toInternalScale($netPay),
                'calculation_snapshot'          => [
                    'faculty_loads_count'  => $employee->is_faculty ? $employee->facultyLoads()->count() : 0,
                    'attendance_logs_days' => $logs->count(),
                    'calculated_at'        => now()->toIso8601String(),
                ],
            ]);

            // Breakdowns
            $breakdowns = [
                ['earning', 'BASIC_PAY', 'Basic Compensation Earned', $basicEarned],
                ['earning', 'OVERLOAD_PAY', 'Faculty Overload Compensation', $overloadPay],
                ['earning', 'DE_MINIMIS', 'Non-taxable De Minimis', $deMinimis],
                ['statutory_ee', 'TARDINESS_UNDERTIME', 'Tardiness & Undertime Deduction', $tardyUndertimeDeduction],
                ['statutory_ee', 'ABSENCE_UNPAID', 'Unpaid Absence Deduction', $absenceDeduction],
                ['statutory_ee', $isPublicSector ? 'GSIS_EE' : 'SSS_EE', $isPublicSector ? 'GSIS EE Share' : 'SSS EE Share', $isPublicSector ? $gsisEe : $sssEe],
                ['statutory_ee', 'PHILHEALTH_EE', 'PhilHealth EE Share', $phEe],
                ['statutory_ee', 'PAGIBIG_EE', 'Pag-IBIG EE Share', $hdmfEe],
                ['tax', 'BIR_WTAX', 'BIR Withholding Tax on Compensation', $withholdingTax],
            ];

            foreach ($breakdowns as [$cat, $code, $desc, $amt]) {
                if (bccomp($amt, '0.0000', self::SCALE) > 0) {
                    PayrollItemBreakdown::create([
                        'payroll_item_id'  => $item->id,
                        'tenant_id'        => $run->tenant_id,
                        'category'         => $cat,
                        'item_code'        => $code,
                        'item_description' => $desc,
                        'amount'           => $amt,
                        'rate_table_version' => '2025/2026_V1',
                    ]);
                }
            }

            return $item;
        });
    }

    /**
     * Finalize and lock a payroll run with a cryptographic HMAC-SHA256 checksum.
     */
    public function lockPayrollRun(PayrollRun $run, string $approverUserId): PayrollRun
    {
        return DB::transaction(function () use ($run, $approverUserId) {
            $items = $run->items()->with('breakdowns')->get();

            $totalGross = '0.0000';
            $totalDeductions = '0.0000';
            $totalNet = '0.0000';
            $totalEr = '0.0000';

            foreach ($items as $item) {
                $totalGross = bcadd($totalGross, (string)$item->gross_pay, self::SCALE);
                $totalDeductions = bcadd($totalDeductions, (string)$item->total_deductions, self::SCALE);
                $totalNet = bcadd($totalNet, (string)$item->net_pay, self::SCALE);
                $erItem = bcadd(bcadd($item->sss_er, $item->gsis_er, self::SCALE), bcadd($item->philhealth_er, $item->pagibig_er, self::SCALE), self::SCALE);
                $totalEr = bcadd($totalEr, $erItem, self::SCALE);
            }

            $serializedData = json_encode([
                'run_id'       => $run->id,
                'period_start' => $run->period_start->toDateString(),
                'period_end'   => $run->period_end->toDateString(),
                'total_gross'  => $totalGross,
                'total_net'    => $totalNet,
                'employee_cnt' => $items->count(),
                'approver'     => $approverUserId,
            ], JSON_THROW_ON_ERROR);

            $checksum = hash_hmac('sha256', $serializedData, config('app.key'));

            $run->update([
                'status'                       => 'locked',
                'approved_by_user_id'          => $approverUserId,
                'approved_at'                  => now(),
                'total_gross_pay'              => $totalGross,
                'total_deductions'             => $totalDeductions,
                'total_net_pay'                => $totalNet,
                'total_employer_contributions' => $totalEr,
                'employee_count'               => $items->count(),
                'checksum_hash'                => $checksum,
            ]);

            return $run;
        });
    }

    private function toInternalScale(string $val): string
    {
        return number_format((float)$val, self::SCALE, '.', '');
    }
}
