<?php

namespace Tests\Unit;

use App\Domain\Disbursement\BankExportEngine;
use App\Domain\Reconciliation\PayrollReconciler;
use App\Domain\Tenancy\TenantContext;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisbursementAndReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_export_engine_generates_valid_transmittal_files(): void
    {
        $tenant = Tenant::create([
            'code'             => 'HEI-BANK',
            'name'             => 'University of the Visayas',
            'institution_type' => 'private_hei',
        ]);
        TenantContext::setTenantId($tenant->id);

        $emp1 = Employee::create([
            'employee_number'        => 'EMP-001',
            'first_name'             => 'Graciano',
            'last_name'              => 'Lopez Jaena',
            'bank_account_encrypted' => '1234567890',
            'date_hired'             => '2020-01-01',
        ]);

        $emp2 = Employee::create([
            'employee_number'        => 'EMP-002',
            'first_name'             => 'Marcelo',
            'last_name'              => 'Del Pilar',
            'bank_account_encrypted' => '9876543210',
            'date_hired'             => '2021-01-01',
        ]);

        $run = PayrollRun::create([
            'period_start'     => '2026-10-01',
            'period_end'       => '2026-10-15',
            'payout_date'      => '2026-10-20',
            'payout_frequency' => 'semi_monthly',
            'status'           => 'approved',
        ]);

        PayrollItem::create([
            'payroll_run_id'   => $run->id,
            'tenant_id'        => $tenant->id,
            'employee_id'      => $emp1->id,
            'gross_pay'        => '25000.0000',
            'total_deductions' => '3500.0000',
            'net_pay'          => '21500.0000',
        ]);

        PayrollItem::create([
            'payroll_run_id'   => $run->id,
            'tenant_id'        => $tenant->id,
            'employee_id'      => $emp2->id,
            'gross_pay'        => '20000.0000',
            'total_deductions' => '2500.0000',
            'net_pay'          => '17500.0000',
        ]);

        $engine = new BankExportEngine();

        // 1. Test Landbank Format
        $lbp = $engine->generate($run, BankExportEngine::BANK_LANDBANK, ['company_code' => 'UV-01']);
        $this->assertEquals(2, $lbp['record_count']);
        $this->assertEquals('39000.00', $lbp['total_amount']);
        $this->assertStringContainsString('H,UV-01', $lbp['content']);
        $this->assertStringContainsString('D,1234567890,Lopez Jaena, Graciano', $lbp['content']);
        $this->assertStringContainsString('T,2,39000.00', $lbp['content']);

        // 2. Test BDO Format
        $bdo = $engine->generate($run, BankExportEngine::BANK_BDO);
        $this->assertEquals(2, $bdo['record_count']);
        $this->assertEquals('39000.00', $bdo['total_amount']);
        $this->assertStringContainsString('Account Number,Employee Name,Amount', $bdo['content']);
        $this->assertStringContainsString('"1234567890","Lopez Jaena  Graciano",21500.00', $bdo['content']);

        // 3. Test BPI BizLink Format
        $bpi = $engine->generate($run, BankExportEngine::BANK_BPI);
        $this->assertEquals(2, $bpi['record_count']);
        $this->assertStringContainsString('ACCOUNT_NUMBER,AMOUNT,EMPLOYEE_ID', $bpi['content']);
        $this->assertStringContainsString('"1234567890",21500.00,"EMP-001"', $bpi['content']);

        TenantContext::clear();
    }

    public function test_payroll_reconciler_accurately_detects_matches_and_variances(): void
    {
        $tenant = Tenant::create([
            'code'             => 'HEI-RECON',
            'name'             => 'Siliman Institute',
            'institution_type' => 'private_hei',
        ]);
        TenantContext::setTenantId($tenant->id);

        $emp1 = Employee::create([
            'employee_number' => 'FAC-01',
            'first_name'      => 'Juan',
            'last_name'       => 'Luna',
            'date_hired'      => '2020-01-01',
        ]);

        $emp2 = Employee::create([
            'employee_number' => 'FAC-02',
            'first_name'      => 'Felix',
            'last_name'       => 'Hidalgo',
            'date_hired'      => '2020-01-01',
        ]);

        $run = PayrollRun::create([
            'period_start'     => '2026-10-01',
            'period_end'       => '2026-10-15',
            'payout_date'      => '2026-10-20',
            'payout_frequency' => 'semi_monthly',
            'status'           => 'approved',
        ]);

        // HERA Item 1: ₱20,325.90
        PayrollItem::create([
            'payroll_run_id'   => $run->id,
            'tenant_id'        => $tenant->id,
            'employee_id'      => $emp1->id,
            'gross_pay'        => '23550.0000',
            'withholding_tax'  => '1836.6000',
            'total_deductions' => '3224.1000',
            'net_pay'          => '20325.9000',
        ]);

        // HERA Item 2: ₱15,000.00
        PayrollItem::create([
            'payroll_run_id'   => $run->id,
            'tenant_id'        => $tenant->id,
            'employee_id'      => $emp2->id,
            'gross_pay'        => '18000.0000',
            'withholding_tax'  => '1200.0000',
            'total_deductions' => '3000.0000',
            'net_pay'          => '15000.0000',
        ]);

        $reconciler = new PayrollReconciler();

        // Mock Legacy Register:
        // EMP 1: Exact Match (₱20,325.90)
        // EMP 2: Rounding Variant of ₱0.02 (₱15,000.02 in legacy vs ₱15,000.00 in HERA)
        // EMP 3: Missing in HERA (triggers material discrepancy)
        $legacyData = [
            [
                'employee_number' => 'FAC-01',
                'gross_pay'       => '23550.00',
                'withholding_tax' => '1836.60',
                'net_pay'         => '20325.90',
            ],
            [
                'employee_number' => 'FAC-02',
                'gross_pay'       => '18000.00',
                'withholding_tax' => '1200.00',
                'net_pay'         => '15000.02',
            ],
            [
                'employee_number' => 'FAC-99', // Missing
                'gross_pay'       => '12000.00',
                'withholding_tax' => '500.00',
                'net_pay'         => '11500.00',
            ],
        ];

        $report = $reconciler->reconcile($run, $legacyData);

        $this->assertEquals(3, $report['total_employees_compared']);
        $this->assertEquals(1, $report['summary']['perfect_matches']);
        $this->assertEquals(1, $report['summary']['rounding_variants']);
        $this->assertEquals(1, $report['summary']['material_discrepancies']);
        $this->assertFalse($report['summary']['is_pilot_passed']);

        // Test that when no missing employee exists and only rounding variants exist, pilot passes!
        $passedLegacyData = array_slice($legacyData, 0, 2);
        $passedReport = $reconciler->reconcile($run, $passedLegacyData);
        $this->assertTrue($passedReport['summary']['is_pilot_passed']);
        $this->assertEquals(0, $passedReport['summary']['material_discrepancies']);

        TenantContext::clear();
    }
}

