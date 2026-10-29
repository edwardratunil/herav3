<?php

namespace Tests\Unit;

use App\Domain\Academic\FacultyLoadCalculator;
use App\Domain\Payroll\Calculators\BirWithholdingTaxCalculator;
use App\Domain\Payroll\Calculators\GsisCalculator;
use App\Domain\Payroll\Calculators\PagIbigCalculator;
use App\Domain\Payroll\Calculators\PhilHealthCalculator;
use App\Domain\Payroll\Calculators\SssCalculator;
use Tests\TestCase;

class PayrollCalculatorsTest extends TestCase
{
    public function test_bir_semimonthly_tax_brackets(): void
    {
        $calc = new BirWithholdingTaxCalculator();

        // Bracket 1: Below ₱10,417
        $this->assertEquals('0.00', $calc->calculateSemiMonthly('10000.00'));

        // Bracket 2: ₱15,000.00 -> 15% of (15,000 - 10,417 = 4,583) = 687.45
        $this->assertEquals('687.45', $calc->calculateSemiMonthly('15000.00'));

        // Bracket 3: ₱25,000.00 -> 937.50 + 20% of (25,000 - 16,667 = 8,333) = 937.50 + 1,666.60 = 2,604.10
        $this->assertEquals('2604.10', $calc->calculateSemiMonthly('25000.00'));

        // Bracket 4: ₱50,000.00 -> 4,270.70 + 25% of (50,000 - 33,333 = 16,667) = 4,270.70 + 4,166.75 = 8,437.45
        $this->assertEquals('8437.45', $calc->calculateSemiMonthly('50000.00'));
    }

    public function test_philhealth_premium_floor_and_ceiling(): void
    {
        $calc = new PhilHealthCalculator();

        // Below floor: ₱7,000 evaluated at ₱10,000 floor -> 5% = ₱500 (₱250 EE, ₱250 ER)
        $floorRes = $calc->calculateMonthly('7000.00');
        $this->assertEquals('250.00', $floorRes['employee_share']);
        $this->assertEquals('250.00', $floorRes['employer_share']);
        $this->assertEquals('500.00', $floorRes['total_premium']);

        // Mid-range: ₱30,000 -> 5% = ₱1,500 (₱750 EE, ₱750 ER)
        $midRes = $calc->calculateMonthly('30000.00');
        $this->assertEquals('750.00', $midRes['employee_share']);
        $this->assertEquals('750.00', $midRes['employer_share']);
        $this->assertEquals('1500.00', $midRes['total_premium']);

        // Above ceiling: ₱120,000 evaluated at ₱100,000 ceiling -> 5% = ₱5,000 (₱2,500 EE, ₱2,500 ER)
        $ceilRes = $calc->calculateMonthly('120000.00');
        $this->assertEquals('2500.00', $ceilRes['employee_share']);
        $this->assertEquals('2500.00', $ceilRes['employer_share']);
        $this->assertEquals('5000.00', $ceilRes['total_premium']);

        // Semi-monthly split for ₱30,000 salary
        $semiRes = $calc->calculateSemiMonthly('30000.00');
        $this->assertEquals('375.00', $semiRes['employee_share']);
        $this->assertEquals('375.00', $semiRes['employer_share']);
    }

    public function test_pagibig_mandatory_cap(): void
    {
        $calc = new PagIbigCalculator();

        // High salary ₱45,000 capped at ₱10,000 fund salary base -> ₱200 EE / ₱200 ER
        $res = $calc->calculateMonthly('45000.00');
        $this->assertEquals('200.00', $res['employee_share']);
        $this->assertEquals('200.00', $res['employer_share']);
        $this->assertEquals('400.00', $res['total_contribution']);

        // Semi-monthly: ₱100 EE / ₱100 ER
        $semi = $calc->calculateSemiMonthly('45000.00');
        $this->assertEquals('100.00', $semi['employee_share']);
        $this->assertEquals('100.00', $semi['employer_share']);
    }

    public function test_sss_regular_and_wisp_contribution(): void
    {
        $calc = new SssCalculator();

        // Regular salary ₱18,000 (MSC = ₱18,000, no WISP)
        // EE = 4.5% of 18k = ₱810.00
        // ER = 9.5% of 18k = ₱1,710.00 + ₱30 EC = ₱1,740.00
        $res = $calc->calculateMonthly('18000.00');
        $this->assertEquals('18000.00', $res['msc_total']);
        $this->assertEquals('810.00', $res['regular_ee']);
        $this->assertEquals('1710.00', $res['regular_er']);
        $this->assertEquals('30.00', $res['ec_er']);
        $this->assertEquals('810.00', $res['total_ee']);
        $this->assertEquals('1740.00', $res['total_er']);

        // High salary ₱40,000 (MSC capped at ₱35,000: ₱20,000 Regular + ₱15,000 WISP)
        // Regular EE = 4.5% of 20k = ₱900.00
        // WISP EE = 4.5% of 15k = ₱675.00
        // Total EE = ₱1,575.00
        $wispRes = $calc->calculateMonthly('40000.00');
        $this->assertEquals('35000.00', $wispRes['msc_total']);
        $this->assertEquals('20000.00', $wispRes['msc_regular']);
        $this->assertEquals('15000.00', $wispRes['msc_wisp']);
        $this->assertEquals('1575.00', $wispRes['total_ee']);
    }

    public function test_gsis_public_school_calculation(): void
    {
        $calc = new GsisCalculator();

        // Teacher I (SG 11, Step 1 ~ ₱27,000)
        // EE = 9% of 27k = ₱2,430.00
        // ER = 12% of 27k = ₱3,240.00
        $res = $calc->calculateMonthly('27000.00');
        $this->assertEquals('2430.00', $res['employee_share']);
        $this->assertEquals('3240.00', $res['employer_share']);
        $this->assertEquals('5670.00', $res['total_premium']);
    }

    public function test_faculty_overload_calculation(): void
    {
        $calc = new FacultyLoadCalculator();

        // 15 lecture units + 6 lab contact hours @ 0.75 lab multiplier = 15 + 4.5 = 19.5 units
        // Regular load cap: 18.00 units
        // Overload: 1.50 units @ ₱850.00/unit = ₱1,275.00
        $res = $calc->calculateOverload('15.00', '6.00', '0.75', '18.00', '850.00');

        $this->assertEquals('19.50', $res['total_teaching_load']);
        $this->assertEquals('18.00', $res['regular_load_credited']);
        $this->assertEquals('1.50', $res['overload_units']);
        $this->assertEquals('1275.0000', $res['overload_pay']);
    }
}
