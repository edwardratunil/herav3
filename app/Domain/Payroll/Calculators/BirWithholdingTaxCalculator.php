<?php

namespace App\Domain\Payroll\Calculators;

/**
 * BIR Withholding Tax on Compensation Calculator
 * In accordance with Republic Act No. 10963 (TRAIN Law)
 * Revised Withholding Tax Tables effective January 1, 2023 onwards.
 */
class BirWithholdingTaxCalculator
{
    public const SCALE_INTERNAL = 4;
    public const SCALE_DISPLAY = 2;

    /**
     * Calculate semi-monthly withholding tax based on net taxable compensation.
     * Net Taxable = Gross Taxable - (SSS/GSIS EE + PhilHealth EE + Pag-IBIG EE + Non-taxable allowances)
     *
     * @param string $netTaxableCompensation String representation of numeric amount
     * @return string Withholding tax rounded to 2 decimal places
     */
    public function calculateSemiMonthly(string $netTaxableCompensation): string
    {
        $taxable = $this->sanitize($netTaxableCompensation);

        if (bccomp($taxable, '10417.0000', self::SCALE_INTERNAL) <= 0) {
            // Bracket 1: ₱0.00 to ₱10,417.00 -> 0.00
            return '0.00';
        }

        if (bccomp($taxable, '16666.0000', self::SCALE_INTERNAL) <= 0) {
            // Bracket 2: ₱10,417.00 to ₱16,666.00 -> 15% in excess over ₱10,417
            $excess = bcsub($taxable, '10417.0000', self::SCALE_INTERNAL);
            $tax = bcmul($excess, '0.1500', self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '33332.0000', self::SCALE_INTERNAL) <= 0) {
            // Bracket 3: ₱16,667.00 to ₱33,332.00 -> ₱937.50 + 20% in excess over ₱16,667
            $excess = bcsub($taxable, '16667.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.2000', self::SCALE_INTERNAL);
            $tax = bcadd('937.5000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '83332.0000', self::SCALE_INTERNAL) <= 0) {
            // Bracket 4: ₱33,333.00 to ₱83,332.00 -> ₱4,270.70 + 25% in excess over ₱33,333
            $excess = bcsub($taxable, '33333.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.2500', self::SCALE_INTERNAL);
            $tax = bcadd('4270.7000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '333332.0000', self::SCALE_INTERNAL) <= 0) {
            // Bracket 5: ₱83,333.00 to ₱333,332.00 -> ₱16,770.70 + 30% in excess over ₱83,333
            $excess = bcsub($taxable, '83333.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.3000', self::SCALE_INTERNAL);
            $tax = bcadd('16770.7000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        // Bracket 6: ₱333,333.00 and above -> ₱91,770.70 + 35% in excess over ₱333,333
        $excess = bcsub($taxable, '333333.0000', self::SCALE_INTERNAL);
        $variable = bcmul($excess, '0.3500', self::SCALE_INTERNAL);
        $tax = bcadd('91770.7000', $variable, self::SCALE_INTERNAL);
        return $this->roundHalfUp($tax);
    }

    /**
     * Calculate monthly withholding tax based on net taxable compensation.
     *
     * @param string $netTaxableCompensation
     * @return string
     */
    public function calculateMonthly(string $netTaxableCompensation): string
    {
        $taxable = $this->sanitize($netTaxableCompensation);

        if (bccomp($taxable, '20833.0000', self::SCALE_INTERNAL) <= 0) {
            return '0.00';
        }

        if (bccomp($taxable, '33332.0000', self::SCALE_INTERNAL) <= 0) {
            $excess = bcsub($taxable, '20833.0000', self::SCALE_INTERNAL);
            $tax = bcmul($excess, '0.1500', self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '66666.0000', self::SCALE_INTERNAL) <= 0) {
            $excess = bcsub($taxable, '33333.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.2000', self::SCALE_INTERNAL);
            $tax = bcadd('1875.0000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '166666.0000', self::SCALE_INTERNAL) <= 0) {
            $excess = bcsub($taxable, '66667.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.2500', self::SCALE_INTERNAL);
            $tax = bcadd('8541.8000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        if (bccomp($taxable, '666666.0000', self::SCALE_INTERNAL) <= 0) {
            $excess = bcsub($taxable, '166667.0000', self::SCALE_INTERNAL);
            $variable = bcmul($excess, '0.3000', self::SCALE_INTERNAL);
            $tax = bcadd('33541.8000', $variable, self::SCALE_INTERNAL);
            return $this->roundHalfUp($tax);
        }

        $excess = bcsub($taxable, '666667.0000', self::SCALE_INTERNAL);
        $variable = bcmul($excess, '0.3500', self::SCALE_INTERNAL);
        $tax = bcadd('183541.8000', $variable, self::SCALE_INTERNAL);
        return $this->roundHalfUp($tax);
    }

    private function sanitize(string $val): string
    {
        $val = trim($val);
        if ($val === '' || !is_numeric($val) || bccomp($val, '0.0000', self::SCALE_INTERNAL) < 0) {
            return '0.0000';
        }
        return bcadd($val, '0.0000', self::SCALE_INTERNAL);
    }

    private function roundHalfUp(string $val): string
    {
        return number_format((float)$val, self::SCALE_DISPLAY, '.', '');
    }
}

