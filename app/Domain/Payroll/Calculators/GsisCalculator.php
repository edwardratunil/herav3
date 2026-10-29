<?php

namespace App\Domain\Payroll\Calculators;

/**
 * Government Service Insurance System (GSIS) Calculator
 * In accordance with Republic Act No. 8291 (The GSIS Act of 1997)
 * Applicable to Public Schools (DepEd, SUCs, LGU-funded institutions)
 * Employee Share: 9.0% of actual monthly basic salary (no ceiling)
 * Employer Share: 12.0% of actual monthly basic salary
 */
class GsisCalculator
{
    public const EE_RATE = '0.0900';
    public const ER_RATE = '0.1200';
    public const SCALE = 4;

    /**
     * Compute monthly GSIS contribution based on actual basic salary.
     *
     * @param string $basicMonthlySalary
     * @return array{employee_share: string, employer_share: string, total_premium: string}
     */
    public function calculateMonthly(string $basicMonthlySalary): array
    {
        $salary = trim($basicMonthlySalary);
        if ($salary === '' || !is_numeric($salary) || bccomp($salary, '0.0000', self::SCALE) <= 0) {
            $salary = '0.0000';
        }

        $ee = bcmul($salary, self::EE_RATE, self::SCALE);
        $er = bcmul($salary, self::ER_RATE, self::SCALE);
        $total = bcadd($ee, $er, self::SCALE);

        return [
            'employee_share' => $this->round($ee),
            'employer_share' => $this->round($er),
            'total_premium'  => $this->round($total),
        ];
    }

    /**
     * Compute semi-monthly GSIS contribution.
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

