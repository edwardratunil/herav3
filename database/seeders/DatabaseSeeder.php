<?php

namespace Database\Seeders;

use App\Domain\Leave\LeaveCreditManager;
use App\Domain\Tenancy\TenantContext;
use App\Models\AcademicTerm;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmploymentContract;
use App\Models\FacultyLoad;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles & Default Permission Sets
        $roles = [
            [
                'id'          => 'super-admin',
                'name'        => 'Super Administrator',
                'description' => 'Full cross-tenant access, system configurations, statutory matrix management, and platform user administration.',
                'is_system'   => true,
                'permissions' => [
                    'tenants.manage'     => true,
                    'users.manage'       => true,
                    'roles.manage'       => true,
                    'statutory.manage'   => true,
                    'payroll.view_all'   => true,
                    'audit.inspect'      => true,
                ],
            ],
            [
                'id'          => 'tenant-admin',
                'name'        => 'Institution Administrator',
                'description' => 'Administrative control of campus branches, departments, academic terms, and institution staff assignments.',
                'is_system'   => true,
                'permissions' => [
                    'organization.manage' => true,
                    'users.assign'        => true,
                    'academic.manage'     => true,
                    'audit.inspect'       => true,
                ],
            ],
            [
                'id'          => 'hr-officer',
                'name'        => 'Human Resources Officer',
                'description' => 'Manages employee 201 records, contracts, onboarding, leave authorizations, and attendance logs.',
                'is_system'   => true,
                'permissions' => [
                    'personnel.manage'   => true,
                    'contracts.manage'   => true,
                    'attendance.verify'  => true,
                    'leave.approve'      => true,
                ],
            ],
            [
                'id'          => 'academic-head',
                'name'        => 'Academic Dean / Dept Head',
                'description' => 'Oversees faculty teaching load distributions, overload authorizations, substitutions, and syllabi.',
                'is_system'   => true,
                'permissions' => [
                    'faculty_load.manage'  => true,
                    'faculty_load.approve' => true,
                    'leave.endorse'        => true,
                ],
            ],
            [
                'id'          => 'payroll-preparer',
                'name'        => 'Payroll Officer (Preparer)',
                'description' => 'Initiates semi-monthly payroll runs, aggregates DTR and overload data, and reviews calculation previews.',
                'is_system'   => true,
                'permissions' => [
                    'payroll.prepare' => true,
                    'payroll.preview' => true,
                    'disbursements.export' => false,
                ],
            ],
            [
                'id'          => 'payroll-approver',
                'name'        => 'Finance Comptroller (Approver)',
                'description' => 'Separation of duties: Reviews audit breakdowns, verifies budget totals, and performs official locking and bank export.',
                'is_system'   => true,
                'permissions' => [
                    'payroll.approve'      => true,
                    'payroll.lock'         => true,
                    'disbursements.export' => true,
                    'reconciliation.run'   => true,
                ],
            ],
            [
                'id'          => 'auditor',
                'name'        => 'Internal / External Auditor',
                'description' => 'Read-only access to historical payroll registers, calculation snapshots, and cryptographic audit trails.',
                'is_system'   => true,
                'permissions' => [
                    'payroll.view_all' => true,
                    'audit.inspect'    => true,
                    'export.reports'   => true,
                ],
            ],
            [
                'id'          => 'employee',
                'name'        => 'Faculty & Staff (ESS)',
                'description' => 'Employee self-service portal: Personal daily time record (DTR), leave requests, workload, and payslips.',
                'is_system'   => true,
                'permissions' => [
                    'ess.view_dtr'      => true,
                    'ess.file_leave'    => true,
                    'ess.view_payslip'  => true,
                    'ess.view_workload' => true,
                ],
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['id' => $r['id']], $r);
        }

        // 2. Create Superadmin User
        $superadmin = User::updateOrCreate(
            ['email' => 'admin@hera.ph'],
            [
                'name'          => 'Super Administrator',
                'password'      => Hash::make('Password123!'),
                'role'          => 'super-admin',
                'is_active'     => true,
                'last_login_at' => now(),
            ]
        );

        // 3. Create Default Pilot Tenant: San Carlos Institute (Private HEI)
        $tenant = Tenant::updateOrCreate(
            ['code' => 'HEI-001'],
            [
                'name'             => 'San Carlos Institute',
                'institution_type' => 'private_hei',
                'is_active'        => true,
                'settings'         => [
                    'day_factor'             => 313,
                    'regular_load_cap_units' => 18.00,
                    'lab_multiplier'         => 0.75,
                ],
            ]
        );

        TenantContext::setTenantId($tenant->id);

        // 4. Create Sample Platform Users (Mirroring the reference design table)
        $sampleUsers = [
            [
                'name'          => 'Sarah Johnson',
                'email'         => 'sarah.johnson@hera.ph',
                'role'          => 'super-admin',
                'is_active'     => true,
                'created_at'    => '2026-03-23 09:15:00',
                'last_login_at' => '2026-10-29 14:00:00',
            ],
            [
                'name'          => 'Kathryn Murphy',
                'email'         => 'kathryn.murphy@sancarlos.edu.ph',
                'role'          => 'tenant-admin',
                'is_active'     => true,
                'created_at'    => '2026-03-23 10:00:00',
                'last_login_at' => '2026-10-28 16:30:00',
            ],
            [
                'name'          => 'Dianne Russell',
                'email'         => 'dianne.russell@sancarlos.edu.ph',
                'role'          => 'payroll-approver',
                'is_active'     => true,
                'created_at'    => '2026-04-10 11:20:00',
                'last_login_at' => '2026-10-27 11:45:00',
            ],
            [
                'name'          => 'Jenny Wilson',
                'email'         => 'jenny.wilson@sancarlos.edu.ph',
                'role'          => 'payroll-preparer',
                'is_active'     => true,
                'created_at'    => '2026-05-06 08:30:00',
                'last_login_at' => '2026-10-29 08:15:00',
            ],
            [
                'name'          => 'Esther Howard',
                'email'         => 'esther.howard@sancarlos.edu.ph',
                'role'          => 'hr-officer',
                'is_active'     => true,
                'created_at'    => '2026-04-28 14:00:00',
                'last_login_at' => '2026-10-25 15:20:00',
            ],
            [
                'name'          => 'Savannah Nguyen',
                'email'         => 'savannah.nguyen@sancarlos.edu.ph',
                'role'          => 'academic-head',
                'is_active'     => false, // Inactive toggle test
                'created_at'    => '2026-06-15 13:45:00',
                'last_login_at' => '2026-09-30 17:00:00',
            ],
        ];

        foreach ($sampleUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'          => $u['name'],
                    'password'      => Hash::make('Password123!'),
                    'role'          => $u['role'],
                    'tenant_id'     => $u['role'] === 'super-admin' ? null : $tenant->id,
                    'is_active'     => $u['is_active'],
                    'created_at'    => $u['created_at'],
                    'last_login_at' => $u['last_login_at'],
                ]
            );
        }

        // 5. Create Campus & Department
        $campus = Campus::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Main Campus'],
            ['is_main_campus' => true, 'address' => 'Cebu City, Philippines']
        );

        $department = Department::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'CAS'],
            ['campus_id' => $campus->id, 'name' => 'College of Arts & Sciences', 'type' => 'academic']
        );

        // 6. Create Standard Academic Term
        $term = AcademicTerm::updateOrCreate(
            ['tenant_id' => $tenant->id, 'school_year' => '2026-2027', 'term_name' => '1st Semester'],
            [
                'start_date' => '2026-08-01',
                'end_date'   => '2026-12-15',
                'is_active'  => true,
            ]
        );

        // 7. Create Default Work Shift
        Shift::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Regular Academic Shift'],
            [
                'code'                 => 'REG-0817',
                'start_time'           => '08:00:00',
                'end_time'             => '17:00:00',
                'break_minutes'        => 60,
                'grace_period_minutes' => 15,
                'is_default'           => true,
            ]
        );

        // 8. Create Statutory Leave Types
        $sl = LeaveType::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'SL'],
            ['name' => 'Sick Leave', 'category' => 'statutory', 'is_paid' => true, 'days_per_year' => '15.00']
        );
        $vl = LeaveType::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'VL'],
            ['name' => 'Vacation Leave', 'category' => 'statutory', 'is_paid' => true, 'days_per_year' => '15.00']
        );
        $solo = LeaveType::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'SOLO_PARENT'],
            ['name' => 'Solo Parent Leave', 'category' => 'statutory', 'is_paid' => true, 'days_per_year' => '7.00']
        );

        // 9. Seed Sample Faculty Member: Prof. Antonio Luna
        $faculty = Employee::updateOrCreate(
            ['tenant_id' => $tenant->id, 'employee_number' => 'FAC-2026-01'],
            [
                'first_name'              => 'Antonio',
                'middle_name'             => 'Placido',
                'last_name'               => 'Luna',
                'email'                   => 'antonio.luna@sancarlos.edu.ph',
                'department_id'           => $department->id,
                'is_faculty'              => true,
                'faculty_rank'            => 'Associate Professor',
                'status'                  => 'active',
                'date_hired'              => '2020-06-01',
                'tin_encrypted'           => '123-456-789-000',
                'sss_no_encrypted'        => '04-1234567-8',
                'philhealth_no_encrypted' => '12-345678901-2',
                'pagibig_no_encrypted'    => '1234-5678-9012',
                'bank_account_encrypted'  => '1234567890',
                'bank_name'               => 'Landbank of the Philippines',
            ]
        );

        EmploymentContract::updateOrCreate(
            ['tenant_id' => $tenant->id, 'employee_id' => $faculty->id],
            [
                'contract_type'        => 'regular',
                'salary_basis'         => 'monthly',
                'base_rate'            => '40000.0000',
                'de_minimis_allowance' => '1000.0000',
                'effective_date'       => '2026-01-01',
                'is_active'            => true,
            ]
        );

        FacultyLoad::updateOrCreate(
            ['tenant_id' => $tenant->id, 'employee_id' => $faculty->id, 'academic_term_id' => $term->id],
            [
                'subject_code'           => 'CHEM-301',
                'subject_title'          => 'Organic Chemistry Lecture & Laboratory',
                'lecture_units'          => '18.00',
                'lab_contact_hours'      => '4.00',
                'lab_multiplier'         => '0.7500',
                'credited_load_units'    => '21.00',
                'overload_rate_per_unit' => '850.0000',
                'is_approved_by_dean'    => true,
                'approved_at'            => now(),
            ]
        );

        $leaveManager = app(LeaveCreditManager::class);
        $leaveManager->allocateAnnualCredits($faculty, $sl, 2026);
        $leaveManager->allocateAnnualCredits($faculty, $vl, 2026);
        $leaveManager->allocateAnnualCredits($faculty, $solo, 2026);

        TenantContext::clear();
    }
}
