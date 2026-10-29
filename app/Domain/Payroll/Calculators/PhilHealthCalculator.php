<?php

namespace App\Domain\Payroll\Calculators;

/**
 * PhilHealth Premium Calculator
 * In accordance with Republic Act No. 11223 (Universal Health Care Act)
 * Premium rate: 5.0% (effective 2024/2025 onwards)
 * Income Floor: ₱10,000.00; Income Ceiling: ₱100,000.00
 * Split: 50% Employee Share, 50% Employer Share
 */
class PhilHealthCalculator
{
    public const RATE = '0.0500';
    public const FLOOR = '10000.0000';
    public const CEILING = '100000.0000';
    public const SCALE = 4;

    /**
     * Compute monthly PhilHealth contribution.
     *
     * @param string $basicMonthlySalary
     * @return array{employee_share: string, employer_share: string, total_premium: string}
     */
    public function calculateMonthly(string $basicMonthlySalary): array
    {
        $salary = trim($basicMonthlySalary);
        if ($salary === '' || !is_numeric($salary) || bccomp($salary, '0.0000', self::SCALE) <= 0) {
            $salary = self::FLOOR;
        }

        // Apply floor and ceiling
        if (bccomp($salary, self::FLOOR, self::SCALE) < 0) {
            $base = self::FLOOR;
        } elseif (bccomp($salary, self::CEILING, self::SCALE) > 0) {
            $base = self::CEILING;
        } else {
            $base = $salary;
        }

        // Total premium = Base * 5.0%
        $totalPremium = bcmul($base, self::RATE, self::SCALE);

        // Split 50/50
        $eeShare = bcdiv($totalPremium, '2.0000', self::SCALE);
        $erShare = bcsub($totalPremium, $eeShare, self::SCALE);

        return [
            'employee_share' => $this->round($eeShare),
            'employer_share' => $this->round($erShare),
            'total_premium'  => $this->round($totalPremium),
        ];
    }

    /**
     * Compute semi-monthly PhilHealth contribution (split into 2 cutoffs).
     *
     * @param string $basicMonthlySalary
     * @return array{employee_share: string, employer_share: string, total_premium: string}
     */
    public function calculateSemiMonthly(string $basicMonthlySalary): array
    {
        $monthly = $this->calculateMonthly($basicMonthlySalary);

        $eeCutoff = bcdiv($monthly['employee_share'], '2.0000', self::SCALE);
        $erCutoff = bcdiv($monthly['employer_share'], '2.0000', self::SCALE);
        $totalCutoff = bcadd($eeCutoff, $erCutoff, self::SCALE);

        return [
            'employee_share' => $this->round($eeCutoff),
            'employer_share' => $this->round($erCutoff),
            'total_premium'  => $this->round($totalCutoff),
        ];
    }

    private function round(string $val): string
    {
        return number_format((float)$val, 2, '.', '');
    }
}

