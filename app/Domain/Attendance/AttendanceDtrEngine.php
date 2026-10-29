<?php

namespace App\Domain\Attendance;

use App\Models\Shift;
use Carbon\Carbon;

/**
 * Attendance & Daily Time Record (DTR) Calculation Engine
 * Computes tardiness, undertime, absence deductions based on Philippine Labor Code & CSC rules.
 */
class AttendanceDtrEngine
{
    public const SCALE = 4;

    /**
     * Common Philippine Educational Institutions Day Factors:
     * - Factor 313: 6-day workweek (Private schools with Saturday duty/classes)
     * - Factor 261: 5-day workweek (DepEd, SUCs, LGU, 5-day private HEIs)
     * - Factor 365: All days considered paid (certain monthly non-teaching roles)
     */
    public const FACTOR_6_DAYS = '313.0000';
    public const FACTOR_5_DAYS = '261.0000';

    /**
     * Evaluate tardiness and undertime minutes for a single attendance day against a shift.
     *
     * @param Carbon $timeIn
     * @param Carbon $timeOut
     * @param Shift $shift
     * @return array{tardiness_minutes: int, undertime_minutes: int}
     */
    public function evaluateDayPunch(Carbon $timeIn, Carbon $timeOut, Shift $shift): array
    {
        $logDate = $timeIn->toDateString();
        $shiftStart = Carbon::parse("{$logDate} {$shift->start_time}");
        $shiftEnd = Carbon::parse("{$logDate} {$shift->end_time}");
        $graceLimit = $shiftStart->copy()->addMinutes($shift->grace_period_minutes);

        // Tardiness calculation:
        $tardinessMinutes = 0;
        if ($timeIn->greaterThan($graceLimit)) {
            // Under Philippine standard practice, exceeding grace period counts minutes from shift start
            $tardinessMinutes = abs((int) $timeIn->diffInMinutes($shiftStart));
        }

        // Undertime calculation:
        $undertimeMinutes = 0;
        if ($timeOut->lessThan($shiftEnd)) {
            $undertimeMinutes = abs((int) $shiftEnd->diffInMinutes($timeOut));
        }

        return [
            'tardiness_minutes' => $tardinessMinutes,
            'undertime_minutes' => $undertimeMinutes,
        ];
    }

    /**
     * Compute daily, hourly, and minute compensation rates from monthly basic salary.
     *
     * @param string $monthlyBasic
     * @param string $factorDays e.g. '313.0000' or '261.0000'
     * @return array{daily_rate: string, hourly_rate: string, minute_rate: string}
     */
    public function computeRates(string $monthlyBasic, string $factorDays = self::FACTOR_6_DAYS): array
    {
        $monthly = trim($monthlyBasic);
        if ($monthly === '' || !is_numeric($monthly) || bccomp($monthly, '0.0000', self::SCALE) <= 0) {
            return [
                'daily_rate'  => '0.0000',
                'hourly_rate' => '0.0000',
                'minute_rate' => '0.0000',
            ];
        }

        // Annual Salary = Monthly * 12
        $annualSalary = bcmul($monthly, '12.0000', self::SCALE);

        // Daily Rate = Annual / Factor
        $dailyRate = bcdiv($annualSalary, $factorDays, self::SCALE);

        // Hourly Rate = Daily Rate / 8 hours
        $hourlyRate = bcdiv($dailyRate, '8.0000', self::SCALE);

        // Minute Rate = Hourly Rate / 60 minutes
        $minuteRate = bcdiv($hourlyRate, '60.0000', self::SCALE);

        return [
            'daily_rate'  => $dailyRate,
            'hourly_rate' => $hourlyRate,
            'minute_rate' => $minuteRate,
        ];
    }

    /**
     * Calculate monetary deduction for accumulated tardiness, undertime, and absences.
     *
     * @param string $monthlyBasic
     * @param int $totalTardinessMinutes
     * @param int $totalUndertimeMinutes
     * @param string $unpaidAbsenceDays Decimal number of days e.g. '1.0', '0.5'
     * @param string $factorDays
     * @return array{
     *   tardiness_deduction: string,
     *   undertime_deduction: string,
     *   absence_deduction: string,
     *   total_attendance_deduction: string
     * }
     */
    public function calculateAttendanceDeduction(
        string $monthlyBasic,
        int $totalTardinessMinutes,
        int $totalUndertimeMinutes,
        string $unpaidAbsenceDays = '0.0000',
        string $factorDays = self::FACTOR_6_DAYS
    ): array {
        $rates = $this->computeRates($monthlyBasic, $factorDays);
        $minuteRate = $rates['minute_rate'];
        $dailyRate = $rates['daily_rate'];

        // Tardiness Deduction = minutes * minute_rate
        $tardyDeduction = bcmul((string)$totalTardinessMinutes, $minuteRate, self::SCALE);

        // Undertime Deduction = minutes * minute_rate
        $undertimeDeduction = bcmul((string)$totalUndertimeMinutes, $minuteRate, self::SCALE);

        // Absence Deduction = unpaid_days * daily_rate
        $absenceDeduction = bcmul($unpaidAbsenceDays, $dailyRate, self::SCALE);

        $total = bcadd(bcadd($tardyDeduction, $undertimeDeduction, self::SCALE), $absenceDeduction, self::SCALE);

        return [
            'tardiness_deduction'        => $this->round($tardyDeduction),
            'undertime_deduction'        => $this->round($undertimeDeduction),
            'absence_deduction'          => $this->round($absenceDeduction),
            'total_attendance_deduction' => $this->round($total),
        ];
    }

    private function round(string $val): string
    {
        return number_format((float)$val, 4, '.', '');
    }
}
