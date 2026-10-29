<?php

namespace Tests\Feature;

use App\Domain\Payroll\PayrollCalculationService;
use App\Domain\Tenancy\TenantContext;
use App\Models\AcademicTerm;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmploymentContract;
use App\Models\FacultyLoad;
use App\Models\PayrollRun;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenancyAndPayrollPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenancy_isolation_prevents_cross_tenant_data_leakage(): void
    {
        $tenantA = Tenant::create([
            'code'             => 'UNIV-A',
            'name'             => 'University of Manila',
            'institution_type' => 'private_hei',
        ]);

        $tenantB = Tenant::create([
            'code'             => 'COLL-B',
            'name'             => 'Cebu Technical College',
            'institution_type' => 'private_hei',
        ]);

        // Create employee in Tenant A context
        TenantContext::setTenantId($tenantA->id);
        $empA = Employee::create([
            'employee_number' => 'EMP-001',
            'first_name'      => 'Juan',
            'last_name'       => 'Dela Cruz',
            'date_hired'      => '2024-01-15',
        ]);

        // Create employee in Tenant B context
        TenantContext::setTenantId($tenantB->id);
        $empB = Employee::create([
            'employee_number' => 'EMP-002',
            'first_name'      => 'Maria',
            'last_name'       => 'Santos',
            'date_hired'      => '2024-02-01',
        ]);

        // When Tenant B is active, Employee::all() should only return Tenant B employee
        $resultsTenantB = Employee::all();
        $this->assertCount(1, $resultsTenantB);
        $this->assertEquals('EMP-002', $resultsTenantB->first()->employee_number);

        // Switch to Tenant A
        TenantContext::setTenantId($tenantA->id);
        $resultsTenantA = Employee::all();
        $this->assertCount(1, $resultsTenantA);
        $this->assertEquals('EMP-001', $resultsTenantA->first()->employee_number);

        TenantContext::clear();
    }

    public function test_sensitive_personal_information_is_encrypted_at_rest(): void
    {
        $tenant = Tenant::create([
            'code'             => 'SCH-01',
            'name'             => 'St. Paul Academy',
            'institution_type' => 'private_k12',
        ]);
        TenantContext::setTenantId($tenant->id);

        $plainTin = '123-456-789-000';
        $plainSss = '04-1234567-8';
        $plainBank = '1234-5678-90';

        $employee = Employee::create([
            'employee_number'         => 'FAC-101',
            'first_name'              => 'Jose',
            'last_name'               => 'Rizal',
            'tin_encrypted'           => $plainTin,
            'sss_no_encrypted'        => $plainSss,
            'bank_account_encrypted'  => $plainBank,
            'date_hired'              => '2024-06-01',
        ]);

        // Verify model access decrypts seamlessly
        $this->assertEquals($plainTin, $employee->tin_encrypted);
        $this->assertEquals($plainSss, $employee->sss_no_encrypted);
        $this->assertEquals($plainBank, $employee->bank_account_encrypted);

        // Verify raw database column does NOT store plaintext (RA 10173 compliance)
        $rawRecord = DB::table('employees')->where('id', $employee->id)->first();
        $this->assertNotEquals($plainTin, $rawRecord->tin_encrypted);
        $this->assertNotEquals($plainSss, $rawRecord->sss_no_encrypted);
        $this->assertNotEquals($plainBank, $rawRecord->bank_account_encrypted);

        TenantContext::clear();
    }

    public function test_full_faculty_payroll_run_calculation_and_locking(): void
    {
        $tenant = Tenant::create([
            'code'             => 'HEI-001',
            'name'             => 'San Carlos Institute',
            'institution_type' => 'private_hei',
        ]);
        TenantContext::setTenantId($tenant->id);

        $term = AcademicTerm::create([
            'school_year' => '2026-2027',
            'term_name'   => '1st Semester',
            'start_date'  => '2026-08-01',
            'end_date'    => '2026-12-15',
            'is_active'   => true,
        ]);

        $employee = Employee::create([
            'employee_number' => 'FAC-2026-01',
            'first_name'      => 'Antonio',
            'last_name'       => 'Luna',
            'is_faculty'      => true,
            'faculty_rank'    => 'Associate Professor',
            'date_hired'      => '2020-08-01',
        ]);

        EmploymentContract::create([
            'employee_id'          => $employee->id,
            'contract_type'        => 'regular',
            'salary_basis'         => 'monthly',
            'base_rate'            => '40000.0000', // ₱40,000 monthly -> ₱20,000 semi-monthly
            'de_minimis_allowance' => '1000.0000',  // ₱1,000 non-taxable
            'taxable_allowance'    => '0.0000',
            'effective_date'       => '2026-01-01',
            'is_active'            => true,
        ]);

        // Faculty Load: 18 lecture units + 4 lab contact hours (@ 0.75 = 3 load units) -> Total 21 units
        // Baseline: 18 units -> 3 units Overload @ ₱850 = ₱2,550.00
        FacultyLoad::create([
            'employee_id'            => $employee->id,
            'academic_term_id'       => $term->id,
            'lecture_units'          => '18.00',
            'lab_contact_hours'      => '4.00',
            'lab_multiplier'         => '0.7500',
            'credited_load_units'    => '21.00',
            'overload_rate_per_unit' => '850.0000',
            'is_approved_by_dean'    => true,
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
        // Basic Semi-monthly: ₱20,000.00
        // Overload: 3 units * ₱850 = ₱2,550.00
        // De Minimis: ₱1,000.00
        // Gross Pay: 20,000 + 2,550 + 1,000 = ₱23,550.00
        $this->assertEquals('20000.0000', $item->basic_earned);
        $this->assertEquals('2550.0000', $item->faculty_overload_pay);
        $this->assertEquals('23550.0000', $item->gross_pay);

        // Statutory checks for ₱40,000 monthly equivalent:
        // SSS: Total MSC ₱35,000 -> Monthly EE = ₱1,575.00 -> Semi-monthly = ₱787.50
        $this->assertEquals('787.5000', $item->sss_ee);
        // PhilHealth: 5% of ₱40,000 = ₱2,000 / 2 = ₱1,000 monthly EE -> Semi-monthly = ₱500.00
        $this->assertEquals('500.0000', $item->philhealth_ee);
        // Pag-IBIG: ₱200 monthly EE -> Semi-monthly = ₱100.00
        $this->assertEquals('100.0000', $item->pagibig_ee);

        // Total Statutory EE = 787.50 + 500.00 + 100.00 = ₱1,387.50
        // Gross Taxable = Gross (23,550) - De Minimis (1,000) - Statutory EE (1,387.50) = ₱21,162.50
        // BIR Bracket 3 (₱16,667 - ₱33,332): 937.50 + 20% of (21,162.50 - 16,667 = 4,495.50)
        // 20% of 4,495.50 = 899.10 -> Tax = 937.50 + 899.10 = ₱1,836.60
        $this->assertEquals('1836.6000', $item->withholding_tax);

        // Total Deductions = 1,387.50 + 1,836.60 = ₱3,224.10
        $this->assertEquals('3224.1000', $item->total_deductions);

        // Net Pay = 23,550.00 - 3,224.10 = ₱20,325.90
        $this->assertEquals('20325.9000', $item->net_pay);

        // Lock the payroll run with cryptographic hash
        $approverId = '00000000-0000-0000-0000-000000000001';
        $lockedRun = $service->lockPayrollRun($run, $approverId);

        $this->assertEquals('locked', $lockedRun->status);
        $this->assertNotEmpty($lockedRun->checksum_hash);
        $this->assertEquals('23550.0000', $lockedRun->total_gross_pay);
        $this->assertEquals('20325.9000', $lockedRun->total_net_pay);
        $this->assertTrue($lockedRun->isLocked());

        TenantContext::clear();
    }
}

