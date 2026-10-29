<?php

namespace App\Domain\Academic;

/**
 * Faculty Workload & Overload Calculation Engine
 * Handles lecture units, laboratory multipliers, regular load baselines, and overload premiums.
 */
class FacultyLoadCalculator
{
    public const SCALE = 4;

    /**
     * Compute total teaching load units and overload compensation.
     *
     * @param string $lectureUnits
     * @param string $labContactHours
     * @param string $labLoadMultiplier e.g. '0.75' (standard Philippine HEI formula: 3 contact hrs = 0.75 - 1.0 load credit)
     * @param string $regularLoadCap Baseline units included in full-time basic salary (e.g. '18.00')
     * @param string $overloadRatePerUnit Hourly or unit overload rate
     * @return array{
     *   lecture_units: string,
     *   lab_load_credits: string,
     *   total_teaching_load: string,
     *   regular_load_credited: string,
     *   overload_units: string,
     *   overload_pay: string
     * }
     */
    public function calculateOverload(
        string $lectureUnits,
        string $labContactHours,
        string $labLoadMultiplier = '0.7500',
        string $regularLoadCap = '18.0000',
        string $overloadRatePerUnit = '850.0000'
    ): array {
        $lec = $this->sanitize($lectureUnits);
        $labHours = $this->sanitize($labContactHours);
        $labMult = $this->sanitize($labLoadMultiplier);
        $cap = $this->sanitize($regularLoadCap);
        $rate = $this->sanitize($overloadRatePerUnit);

        // Lab load credit = lab_hours * lab_multiplier
        $labCredits = bcmul($labHours, $labMult, self::SCALE);
        
        // Total load = lecture_units + lab_load_credits
        $totalLoad = bcadd($lec, $labCredits, self::SCALE);

        // Overload determination
        if (bccomp($totalLoad, $cap, self::SCALE) > 0) {
            $regularCredited = $cap;
            $overloadUnits = bcsub($totalLoad, $cap, self::SCALE);
            $overloadPay = bcmul($overloadUnits, $rate, self::SCALE);
        } else {
            $regularCredited = $totalLoad;
            $overloadUnits = '0.0000';
            $overloadPay = '0.0000';
        }

        return [
            'lecture_units'          => $this->round($lec, 2),
            'lab_load_credits'       => $this->round($labCredits, 2),
            'total_teaching_load'    => $this->round($totalLoad, 2),
            'regular_load_credited'  => $this->round($regularCredited, 2),
            'overload_units'         => $this->round($overloadUnits, 2),
            'overload_pay'           => $this->round($overloadPay, 4),
        ];
    }

    private function sanitize(string $val): string
    {
        $val = trim($val);
        if ($val === '' || !is_numeric($val) || bccomp($val, '0.0000', self::SCALE) < 0) {
            return '0.0000';
        }
        return bcadd($val, '0.0000', self::SCALE);
    }

    private function round(string $val, int $scale = 2): string
    {
        return number_format((float)$val, $scale, '.', '');
    }
}
