<?php

namespace App\Domain\Payroll\Calculators;

/**
 * Social Security System (SSS) Contribution Calculator
 * In accordance with Republic Act No. 11199 (Social Security Act of 2018)
 * 14.5% Contribution Rate (4.5% Employee, 9.5% Employer)
 * Minimum MSC: ₱4,000 | Maximum Regular MSC: ₱20,000 | Maximum Total MSC (with WISP): ₱35,000
 */
class SssCalculator
{
    public const SCALE = 4;

    /**
     * Determine Monthly Salary Credit (MSC) and contributions based on basic monthly salary.
     *
     * @param string $monthlySalary
     * @return array{
     *   msc_regular: string,
     *   msc_wisp: string,
     *   msc_total: string,
     *   regular_ee: string,
     *   regular_er: string,
     *   wisp_ee: string,
     *   wisp_er: string,
     *   ec_er: string,
     *   total_ee: string,
     *   total_er: string,
     *   grand_total: string
     * }
     */
    public function calculateMonthly(string $monthlySalary): array
    {
        $salary = trim($monthlySalary);
        if ($salary === '' || !is_numeric($salary) || bccomp($salary, '0.0000', self::SCALE) <= 0) {
            $salary = '4000.0000';
        }

        // Bracket determination from ₱4,000 up to ₱35,000 in ₱500 steps
        $totalMsc = $this->determineMsc($salary);

        // Regular MSC is capped at ₱20,000
        $regularMsc = bccomp($totalMsc, '20000.0000', self::SCALE) > 0 ? '20000.0000' : $totalMsc;
        
        // WISP MSC is the excess above ₱20,000 up to ₱35,000
        $wispMsc = bccomp($totalMsc, '20000.0000', self::SCALE) > 0 
            ? bcsub($totalMsc, '20000.0000', self::SCALE) 
            : '0.0000';

        // Regular SSS: 4.5% EE, 9.5% ER
        $regularEe = bcmul($regularMsc, '0.0450', self::SCALE);
        $regularEr = bcmul($regularMsc, '0.0950', self::SCALE);

        // WISP (Mandatory Provident Fund): 4.5% EE, 9.5% ER of WISP MSC
        $wispEe = bcmul($wispMsc, '0.0450', self::SCALE);
        $wispEr = bcmul($wispMsc, '0.0950', self::SCALE);

        // EC (Employees' Compensation) paid solely by Employer:
        // ₱10 for MSC <= ₱14,500; ₱30 for MSC > ₱14,500
        $ecEr = bccomp($totalMsc, '14500.0000', self::SCALE) <= 0 ? '10.0000' : '30.0000';

        $totalEe = bcadd($regularEe, $wispEe, self::SCALE);
        $totalEr = bcadd(bcadd($regularEr, $wispEr, self::SCALE), $ecEr, self::SCALE);
        $grandTotal = bcadd($totalEe, $totalEr, self::SCALE);

        return [
            'msc_regular' => $this->round($regularMsc),
            'msc_wisp'    => $this->round($wispMsc),
            'msc_total'   => $this->round($totalMsc),
            'regular_ee'  => $this->round($regularEe),
            'regular_er'  => $this->round($regularEr),
            'wisp_ee'     => $this->round($wispEe),
            'wisp_er'     => $this->round($wispEr),
            'ec_er'       => $this->round($ecEr),
            'total_ee'    => $this->round($totalEe),
            'total_er'    => $this->round($totalEr),
            'grand_total' => $this->round($grandTotal),
        ];
    }

    /**
     * Compute semi-monthly SSS contribution (split equally).
     *
     * @param string $monthlySalary
     * @return array{
     *   total_ee: string,
     *   total_er: string,
     *   grand_total: string
     * }
     */
    public function calculateSemiMonthly(string $monthlySalary): array
    {
        $monthly = $this->calculateMonthly($monthlySalary);

        $eeCutoff = bcdiv($monthly['total_ee'], '2.0000', self::SCALE);
        $erCutoff = bcdiv($monthly['total_er'], '2.0000', self::SCALE);
        $totalCutoff = bcadd($eeCutoff, $erCutoff, self::SCALE);

        return [
            'total_ee'    => $this->round($eeCutoff),
            'total_er'    => $this->round($erCutoff),
            'grand_total' => $this->round($totalCutoff),
        ];
    }

    private function determineMsc(string $salary): string
    {
        if (bccomp($salary, '4250.0000', self::SCALE) < 0) {
            return '4000.0000';
        }
        if (bccomp($salary, '34750.0000', self::SCALE) >= 0) {
            return '35000.0000';
        }

        // Between 4,250 and 34,749.99: bracket steps are every ₱500, starting with MSC 4,500
        // Formula: MSC = 500 * floor((salary - 4250) / 500) + 4500
        $offset = bcsub($salary, '4250.0000', self::SCALE);
        $step = bcdiv($offset, '500.0000', 0); // integer steps
        $bracket = bcadd(bcmul($step, '500.0000', self::SCALE), '4500.0000', self::SCALE);

        return $bracket;
    }

    private function round(string $val): string
    {
        return number_format((float)$val, 2, '.', '');
    }
}

