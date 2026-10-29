<?php

namespace App\Domain\Payroll\Calculators;

/**
 * Pag-IBIG (HDMF) Premium Calculator
 * In accordance with HDMF Circular No. 460
 * Effective February 2024 onwards:
 * Maximum fund salary base: ₱10,000.00
 * Employee Rate: 2.0% (Cap: ₱200.00)
 * Employer Rate: 2.0% (Cap: ₱200.00)
 * Total mandatory monthly contribution: ₱400.00
 */
class PagIbigCalculator
{
    public const MAX_COMPENSATION_BASE = '10000.0000';
    public const MANDATORY_CAP = '200.0000';
    public const SCALE = 4;

    /**
     * Compute monthly Pag-IBIG contribution.
     *
     * @param string $basicMonthlySalary
     * @param string|null $voluntaryAdditionalEe Additional voluntary savings (e.g. MP2 or upgraded regular)
     * @return array{employee_share: string, employer_share: string, total_contribution: string}
     */
    public function calculateMonthly(string $basicMonthlySalary, ?string $voluntaryAdditionalEe = '0.00'): array
    {
        $salary = trim($basicMonthlySalary);
        if ($salary === '' || !is_numeric($salary) || bccomp($salary, '0.0000', self::SCALE) <= 0) {
            $salary = '0.0000';
        }

        if (bccomp($salary, '1500.0000', self::SCALE) <= 0) {
            $eeRate = '0.0100'; // 1%
        } else {
            $eeRate = '0.0200'; // 2%
        }
        $erRate = '0.0200'; // 2%

        // Base capped at ₱10,000
        $base = (bccomp($salary, self::MAX_COMPENSATION_BASE, self::SCALE) > 0)
            ? self::MAX_COMPENSATION_BASE
            : $salary;

        $mandatoryEe = bcmul($base, $eeRate, self::SCALE);
        if (bccomp($mandatoryEe, self::MANDATORY_CAP, self::SCALE) > 0) {
            $mandatoryEe = self::MANDATORY_CAP;
        }

        $mandatoryEr = bcmul($base, $erRate, self::SCALE);
        if (bccomp($mandatoryEr, self::MANDATORY_CAP, self::SCALE) > 0) {
            $mandatoryEr = self::MANDATORY_CAP;
        }

        $voluntary = (is_numeric($voluntaryAdditionalEe) && bccomp($voluntaryAdditionalEe, '0.00', self::SCALE) > 0)
            ? $voluntaryAdditionalEe
            : '0.0000';

        $totalEe = bcadd($mandatoryEe, $voluntary, self::SCALE);
        $totalAll = bcadd($totalEe, $mandatoryEr, self::SCALE);

        return [
            'employee_share' => $this->round($totalEe),
            'employer_share' => $this->round($mandatoryEr),
            'total_contribution' => $this->round($totalAll),
        ];
    }

    /**
     * Compute semi-monthly Pag-IBIG contribution.
     *
     * @param string $basicMonthlySalary
     * @param string|null $voluntaryAdditionalEe
     * @return array{employee_share: string, employer_share: string, total_contribution: string}
     */
    public function calculateSemiMonthly(string $basicMonthlySalary, ?string $voluntaryAdditionalEe = '0.00'): array
    {
        $monthly = $this->calculateMonthly($basicMonthlySalary, $voluntaryAdditionalEe);

        $eeCutoff = bcdiv($monthly['employee_share'], '2.0000', self::SCALE);
        $erCutoff = bcdiv($monthly['employer_share'], '2.0000', self::SCALE);
        $totalCutoff = bcadd($eeCutoff, $erCutoff, self::SCALE);

        return [
            'employee_share' => $this->round($eeCutoff),
            'employer_share' => $this->round($erCutoff),
            'total_contribution' => $this->round($totalCutoff),
        ];
    }

    private function round(string $val): string
    {
        return number_format((float)$val, 2, '.', '');
    }
}

