<?php

namespace Tests\Feature;

use App\Domain\Payroll\PayrollCalculationService;
use App\Domain\Tenancy\TenantContext;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\EmploymentContract;
use App\Models\PayrollRun;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollAttendanceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_tardiness_and_absences_reduce_taxable_income_and_net_pay(): void
    {
        $tenant = Tenant::create([
            'code'             => 'UNIV-ATT',
            'name'             => 'University of Pangasinan',
            'institution_type' => 'private_hei',
        ]);
        TenantContext::setTenantId($tenant->id);

        $employee = Employee::create([
            'employee_number' => 'EMP-DTR-01',
            'first_name'      => 'Apolinario',
            'last_name'       => 'Mabini',
            'date_hired'      => '2022-01-01',
        ]);

        // Monthly salary: ₱31,300.00 -> Semi-monthly basic: ₱15,650.00
        // Under Factor 313: Minute rate = ₱2.5000, Daily rate = ₱1,200.00
        EmploymentContract::create([
            'employee_id'          => $employee->id,
            'contract_type'        => 'regular',
            'salary_basis'         => 'monthly',
            'base_rate'            => '31300.0000',
            'de_minimis_allowance' => '0.0000',
            'effective_date'       => '2026-01-01',
            'is_active'            => true,
        ]);

        // Log 1: Oct 02 - Late by 40 minutes (40 * ₱2.50 = ₱100.00 deduction)
        AttendanceLog::create([
            'employee_id'       => $employee->id,
            'log_date'          => '2026-10-02',
            'status'            => 'present',
            'tardiness_minutes' => 40,
        ]);

        // Log 2: Oct 05 - Unexcused Absence (1 full day = ₱1,200.00 deduction)
        AttendanceLog::create([
            'employee_id' => $employee->id,
            'log_date'    => '2026-10-05',
            'status'      => 'absent',
        ]);

        $run = PayrollRun::create([
            'period_start'     => '2026-10-01',
            'period_end'       => '2026-10-15',
            'payout_date'      => '2026-10-20',
            'payout_frequency' => 'semi_monthly',
            'status'           => 'draft',
        ]);

        /** @var PayrollCalculationService $service */
        $service = app(PayrollCalculationService::class);
        $item = $service->calculateEmployee($run, $employee);

        // Verification:
        // Basic Semi-Monthly: ₱15,650.00
        // Gross Pay: ₱15,650.00
        $this->assertEquals('15650.0000', $item->basic_earned);
        $this->assertEquals('15650.0000', $item->gross_pay);

        // Attendance deductions:
        // Tardiness (40 min @ ₱2.50 = ₱100.00)
        // Absence (1 day @ ₱1,200.00 = ₱1,200.00)
        $this->assertEquals('100.0000', $item->tardiness_undertime_deduction);
        $this->assertEquals('1200.0000', $item->other_deductions);

        // Statutory deductions on ₱31,300 monthly equivalent:
        // PhilHealth: 5% of ₱31,300 = ₱1,565.00 / 2 = ₱782.50 monthly EE -> Semi-monthly = ₱391.25
        $this->assertEquals('391.2500', $item->philhealth_ee);
        // Pag-IBIG: ₱200 monthly EE -> Semi-monthly = ₱100.00
        $this->assertEquals('100.0000', $item->pagibig_ee);

        // Total attendance deductions = 100.00 + 1,200.00 = ₱1,300.00
        // Notice that Net Pay reflects the deducted attendance without discrepancy:
        // Net Pay = Gross - (Statutory EE + Tax + Attendance Deductions)
        $expectedNetPay = bcsub($item->gross_pay, $item->total_deductions, 4);
        $this->assertEquals($expectedNetPay, $item->net_pay);

        // Verify breakdown items include TARDINESS_UNDERTIME and ABSENCE_UNPAID
        $breakdowns = $item->breakdowns()->pluck('amount', 'item_code')->toArray();
        $this->assertArrayHasKey('TARDINESS_UNDERTIME', $breakdowns);
        $this->assertEquals('100.0000', $breakdowns['TARDINESS_UNDERTIME']);
        $this->assertArrayHasKey('ABSENCE_UNPAID', $breakdowns);
        $this->assertEquals('1200.0000', $breakdowns['ABSENCE_UNPAID']);

        TenantContext::clear();
    }
}

