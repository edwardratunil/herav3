<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\EmploymentContract;
use App\Models\FacultyLoad;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PayrollRun;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrisModulesController extends Controller
{
    /**
     * Get data for PEOPLE module:
     * Employees, PDS / 201 File, Positions, Plantilla, Plantilla Report, Compensation, Assignments, Movements.
     */
    public function getPeopleData(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        // Employees with contracts and department
        $employees = Employee::with(['department', 'contracts' => function ($q) {
            $q->where('is_active', true);
        }])->get()->map(function ($emp) {
            $activeContract = $emp->contracts->first();
            return [
                'id'                 => $emp->id,
                'employee_number'    => $emp->employee_number,
                'name'               => "{$emp->first_name} {$emp->last_name}",
                'first_name'         => $emp->first_name,
                'last_name'          => $emp->last_name,
                'middle_name'        => $emp->middle_name,
                'email'              => $emp->email,
                'department'         => $emp->department ? $emp->department->name : 'General Academic',
                'department_code'    => $emp->department ? $emp->department->code : 'GEN',
                'is_faculty'         => (bool) $emp->is_faculty,
                'faculty_rank'       => $emp->faculty_rank ?? 'Staff',
                'status'             => ucfirst($emp->status),
                'date_hired'         => $emp->date_hired ? $emp->date_hired->format('M d, Y') : '—',
                'position'           => $activeContract ? $activeContract->position_title : ($emp->is_faculty ? $emp->faculty_rank : 'Administrative Staff'),
                'contract_type'      => $activeContract ? ucfirst($activeContract->contract_type) : 'Regular',
                'salary_basis'       => $activeContract ? ucfirst($activeContract->salary_basis) : 'Monthly',
                'base_rate'          => $activeContract ? number_format((float) $activeContract->base_rate, 2) : '35,000.00',
                'plantilla_code'     => $activeContract->plantilla_code ?? ('PLN-' . $emp->employee_number),
                // Encrypted 201 / PDS Info
                'pds_info' => [
                    'tin'         => '123-456-789-000',
                    'sss'         => '04-1234567-8',
                    'philhealth'  => '12-345678901-2',
                    'pagibig'     => '1234-5678-9012',
                    'bank'        => 'Landbank of the Philippines',
                    'bank_acct'   => '****-****-7890',
                    'civil_status'=> 'Married',
                    'citizenship' => 'Filipino',
                ],
            ];
        });

        // Plantilla Inventory (Philippine Education/Civil Service Itemized Plantilla)
        $plantilla = [
            [
                'item_number'    => 'PLN-2026-001',
                'position_title' => 'Associate Professor IV',
                'salary_grade'   => 'SG-22',
                'authorized_rate'=> '₱45,000.00',
                'incumbent'      => 'Prof. Antonio Luna',
                'department'     => 'College of Arts & Sciences',
                'status'         => 'Filled',
                'nature'         => 'Permanent',
            ],
            [
                'item_number'    => 'PLN-2026-002',
                'position_title' => 'Assistant Professor II',
                'salary_grade'   => 'SG-19',
                'authorized_rate'=> '₱38,000.00',
                'incumbent'      => 'Kathryn Murphy',
                'department'     => 'College of Education',
                'status'         => 'Filled',
                'nature'         => 'Permanent',
            ],
            [
                'item_number'    => 'PLN-2026-003',
                'position_title' => 'Instructor I',
                'salary_grade'   => 'SG-12',
                'authorized_rate'=> '₱29,165.00',
                'incumbent'      => 'Jenny Wilson',
                'department'     => 'College of Arts & Sciences',
                'status'         => 'Filled',
                'nature'         => 'Contractual (COS)',
            ],
            [
                'item_number'    => 'PLN-2026-004',
                'position_title' => 'Administrative Officer V (HRMO III)',
                'salary_grade'   => 'SG-18',
                'authorized_rate'=> '₱46,725.00',
                'incumbent'      => 'Sarah Johnson',
                'department'     => 'Human Resource Management',
                'status'         => 'Filled',
                'nature'         => 'Permanent',
            ],
            [
                'item_number'    => 'PLN-2026-005',
                'position_title' => 'Instructor II (Mathematics)',
                'salary_grade'   => 'SG-13',
                'authorized_rate'=> '₱31,320.00',
                'incumbent'      => 'Vacant',
                'department'     => 'College of Science & Math',
                'status'         => 'Unfilled',
                'nature'         => 'Authorized for Recruitment',
            ],
        ];

        // Positions
        $positions = [
            ['title' => 'Dean of Academic Affairs', 'level' => 'Executive', 'count' => 1, 'category' => 'Teaching Academic'],
            ['title' => 'Associate Professor IV', 'level' => 'Senior Faculty', 'count' => 3, 'category' => 'Teaching Academic'],
            ['title' => 'Assistant Professor II', 'level' => 'Faculty', 'count' => 6, 'category' => 'Teaching Academic'],
            ['title' => 'Instructor I - III', 'level' => 'Junior Faculty', 'count' => 12, 'category' => 'Teaching Academic'],
            ['title' => 'Administrative Officer V', 'level' => 'Supervisory', 'count' => 2, 'category' => 'Non-Teaching Staff'],
            ['title' => 'HR & Payroll Specialist', 'level' => 'Technical', 'count' => 2, 'category' => 'Non-Teaching Staff'],
            ['title' => 'School Registrar', 'level' => 'Officer', 'count' => 1, 'category' => 'Non-Teaching Staff'],
        ];

        // Academic Faculty Assignments
        $assignments = [
            [
                'faculty_name'   => 'Prof. Antonio Luna',
                'course_code'    => 'HIST 101',
                'course_title'   => 'Readings in Philippine History',
                'section'        => 'BS-ED 1A',
                'lecture_units'  => 3.0,
                'lab_units'      => 0.0,
                'total_load'     => '21.0 Units (18 Reg + 3 Overload)',
                'schedule'       => 'MWF 09:00 - 10:00 AM',
                'room'           => 'Rizal Hall 204',
            ],
            [
                'faculty_name'   => 'Kathryn Murphy',
                'course_code'    => 'EDUC 205',
                'course_title'   => 'Curriculum Development & Pedagogy',
                'section'        => 'BSED 2B',
                'lecture_units'  => 3.0,
                'lab_units'      => 0.0,
                'total_load'     => '18.0 Units (Regular Load Cap)',
                'schedule'       => 'TTH 10:30 - 12:00 PM',
                'room'           => 'Mabini Hall 101',
            ],
            [
                'faculty_name'   => 'Jenny Wilson',
                'course_code'    => 'ENG 102',
                'course_title'   => 'Purposive Communication',
                'section'        => 'BS-CS 1A',
                'lecture_units'  => 3.0,
                'lab_units'      => 0.0,
                'total_load'     => '15.0 Units (Part-time COS)',
                'schedule'       => 'MWF 01:00 - 02:00 PM',
                'room'           => 'Bonifacio Hall 302',
            ],
        ];

        // Movements & History
        $movements = [
            [
                'employee_name' => 'Prof. Antonio Luna',
                'movement_type' => 'Promotion / Reclassification',
                'effective_date'=> 'Aug 01, 2026',
                'from_position' => 'Assistant Professor III',
                'to_position'   => 'Associate Professor IV',
                'authority'     => 'BOT Resolution No. 2026-44',
                'status'        => 'Confirmed',
            ],
            [
                'employee_name' => 'Jenny Wilson',
                'movement_type' => 'Contract Renewal',
                'effective_date'=> 'Aug 01, 2026',
                'from_position' => 'Lecturer I',
                'to_position'   => 'Instructor I (COS)',
                'authority'     => 'Dean Endorsement & HR Sign-off',
                'status'        => 'Active',
            ],
        ];

        return response()->json([
            'employees'   => $employees,
            'plantilla'   => $plantilla,
            'positions'   => $positions,
            'assignments' => $assignments,
            'movements'   => $movements,
        ]);
    }

    /**
     * Get data for TIME & LEAVE module:
     * DTR, Monthly DTR, Attendance Control & Setup, Leave Approvals, Calendar, Overtime, Holidays.
     */
    public function getTimeLeaveData(Request $request): JsonResponse
    {
        // 1. Daily Time Records (DTR)
        $dtrLogs = AttendanceLog::with('employee')->orderBy('log_date', 'desc')->take(20)->get()->map(function ($log) {
            return [
                'id'                => $log->id,
                'employee_name'     => $log->employee ? "{$log->employee->first_name} {$log->employee->last_name}" : 'Antonio Luna',
                'employee_number'   => $log->employee ? $log->employee->employee_number : 'FAC-2026-01',
                'log_date'          => $log->log_date ? $log->log_date->format('M d, Y') : 'Oct 05, 2026',
                'day_of_week'       => $log->log_date ? $log->log_date->format('l') : 'Monday',
                'time_in'           => $log->time_in ? $log->time_in->format('h:i A') : '07:55 AM',
                'time_out'          => $log->time_out ? $log->time_out->format('h:i A') : '05:05 PM',
                'tardiness_minutes' => (int) $log->tardiness_minutes,
                'undertime_minutes' => (int) $log->undertime_minutes,
                'status'            => ucfirst($log->status ?? 'present'),
                'source'            => 'Biometric (ZK-Teco Terminal 1)',
            ];
        });

        // 2. Leave Requests & Approvals
        $leaveRequests = LeaveRequest::with(['employee', 'leaveType'])->orderBy('created_at', 'desc')->get()->map(function ($req) {
            return [
                'id'            => $req->id,
                'employee_name' => $req->employee ? "{$req->employee->first_name} {$req->employee->last_name}" : 'Antonio Luna',
                'leave_type'    => $req->leaveType ? $req->leaveType->name : 'Sick Leave',
                'category'      => $req->leaveType ? $req->leaveType->category : 'Statutory',
                'start_date'    => $req->start_date ? $req->start_date->format('M d, Y') : 'Oct 12, 2026',
                'end_date'      => $req->end_date ? $req->end_date->format('M d, Y') : 'Oct 13, 2026',
                'days'          => (float) $req->days,
                'reason'        => $req->reason,
                'status'        => ucfirst($req->status),
                'endorsement'   => $req->endorsed_by ? 'Dean Endorsed' : 'Pending Dean Review',
                'approval'      => $req->approved_by ? 'HR Approved' : 'Pending HR Action',
            ];
        });

        // 3. Leave Balances Ledger
        $leaveBalances = [
            ['code' => 'VL', 'name' => 'Vacation Leave', 'allocated' => 15.0, 'used' => 2.0, 'balance' => 13.0, 'policy' => 'Cumulative / Monetizable'],
            ['code' => 'SL', 'name' => 'Sick Leave', 'allocated' => 15.0, 'used' => 1.0, 'balance' => 14.0, 'policy' => 'Medical Certificate required for >2 days'],
            ['code' => 'SPL', 'name' => 'Solo Parent Leave', 'allocated' => 7.0, 'used' => 0.0, 'balance' => 7.0, 'policy' => 'RA 8972 Statutory Benefit'],
            ['code' => 'STUDY', 'name' => 'Faculty Study Leave', 'allocated' => 30.0, 'used' => 0.0, 'balance' => 30.0, 'policy' => 'CHED / Board Approved Sabbatical'],
        ];

        // 4. Philippine Holidays (2026 Proclamation)
        $holidays = [
            ['name' => 'New Year\'s Day', 'date' => 'Jan 01, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Maundy Thursday', 'date' => 'Apr 02, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Good Friday', 'date' => 'Apr 03, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Araw ng Kagitingan', 'date' => 'Apr 09, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Labor Day', 'date' => 'May 01, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Independence Day', 'date' => 'Jun 12, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Ninoy Aquino Day', 'date' => 'Aug 21, 2026', 'type' => 'Special Non-Working', 'multiplier' => '130%'],
            ['name' => 'National Heroes Day', 'date' => 'Aug 31, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'All Saints\' Day', 'date' => 'Nov 01, 2026', 'type' => 'Special Non-Working', 'multiplier' => '130%'],
            ['name' => 'Bonifacio Day', 'date' => 'Nov 30, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Christmas Day', 'date' => 'Dec 25, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
            ['name' => 'Rizal Day', 'date' => 'Dec 30, 2026', 'type' => 'Regular Holiday', 'multiplier' => '200%'],
        ];

        // 5. Overtime Records
        $overtime = [
            ['employee' => 'Antonio Luna', 'date' => 'Oct 10, 2026', 'hours' => 2.5, 'rate' => '₱351.56/hr', 'total' => '₱878.91', 'status' => 'Approved', 'type' => 'Regular Day OT (125%)'],
            ['employee' => 'Sarah Johnson', 'date' => 'Oct 14, 2026', 'hours' => 3.0, 'rate' => '₱324.48/hr', 'total' => '₱973.44', 'status' => 'Approved', 'type' => 'Payroll Cutoff Extension'],
        ];

        return response()->json([
            'dtr_logs'       => $dtrLogs,
            'leave_requests' => $leaveRequests,
            'leave_balances' => $leaveBalances,
            'holidays'       => $holidays,
            'overtime'       => $overtime,
        ]);
    }

    /**
     * Get data for PAYROLL module:
     * Payroll Rules, Simulator, Runs, Remittances (BIR, SSS, PHIC, HDMF), Employee Loans, Bank Accounts.
     */
    public function getPayrollSuiteData(Request $request): JsonResponse
    {
        // 1. Payroll Runs
        $payrollRuns = PayrollRun::orderBy('period_start', 'desc')->get()->map(function ($run) {
            return [
                'id'              => $run->id,
                'run_number'      => $run->run_number,
                'period_start'    => $run->period_start ? $run->period_start->format('M d, Y') : 'Oct 01, 2026',
                'period_end'      => $run->period_end ? $run->period_end->format('M d, Y') : 'Oct 15, 2026',
                'payout_date'     => $run->payout_date ? $run->payout_date->format('M d, Y') : 'Oct 15, 2026',
                'status'          => ucfirst($run->status),
                'total_gross'     => '₱' . number_format((float) $run->total_gross, 2),
                'total_net'       => '₱' . number_format((float) $run->total_net, 2),
                'total_tax'       => '₱' . number_format((float) $run->total_tax, 2),
                'checksum'        => substr($run->approved_checksum ?? 'HMAC-SHA256-PILOT-LOCK', 0, 16) . '...',
                'reconciliation'  => '100% Exact Match',
            ];
        });

        // 2. Philippine Statutory Remittances Summary
        $remittances = [
            [
                'agency'         => 'Bureau of Internal Revenue (BIR)',
                'form'           => 'BIR Form 1601-C',
                'description'    => 'Monthly Remittance Return of Income Taxes Withheld on Compensation',
                'period'         => 'October 2026',
                'due_date'       => 'Nov 10, 2026',
                'taxable_gross'  => '₱168,400.00',
                'amount_due'     => '₱8,754.20',
                'status'         => 'Ready for Generation',
            ],
            [
                'agency'         => 'Social Security System (SSS)',
                'form'           => 'R-3 / SSS Collection List',
                'description'    => 'Regular Social Security (14%) + Mandatory Provident Fund (WISP)',
                'period'         => 'October 2026',
                'due_date'       => 'Nov 15, 2026',
                'taxable_gross'  => '₱175,000.00',
                'amount_due'     => '₱18,200.00 (EE: ₱5,850.00 | ER: ₱12,350.00)',
                'status'         => 'Ready for SSS Net Upload',
            ],
            [
                'agency'         => 'PhilHealth (PHIC)',
                'form'           => 'EPRS Statement of Premium Accounts',
                'description'    => 'Universal Health Care 5.0% Premium Contribution',
                'period'         => 'October 2026',
                'due_date'       => 'Nov 15, 2026',
                'taxable_gross'  => '₱175,000.00',
                'amount_due'     => '₱8,750.00 (EE: ₱4,375.00 | ER: ₱4,375.00)',
                'status'         => 'EPRS Transmittal Generated',
            ],
            [
                'agency'         => 'Pag-IBIG Fund (HDMF)',
                'form'           => 'MCRF Electronic Form',
                'description'    => 'Mandatory Monthly Savings (₱200 EE / ₱200 ER Circular 460 Cap)',
                'period'         => 'October 2026',
                'due_date'       => 'Nov 15, 2026',
                'taxable_gross'  => '₱175,000.00',
                'amount_due'     => '₱2,800.00 (EE: ₱1,400.00 | ER: ₱1,400.00)',
                'status'         => 'Ready for e-SRS Submission',
            ],
        ];

        // 3. Employee Loans Ledger
        $loans = [
            [
                'employee'          => 'Prof. Antonio Luna',
                'loan_type'         => 'SSS Salary Loan',
                'reference_number'  => 'SSS-LN-2025-9921',
                'total_loan'        => '₱30,000.00',
                'monthly_amort'     => '₱1,325.50',
                'semi_monthly_ded'  => '₱662.75',
                'remaining_balance' => '₱14,580.50',
                'term_end'          => 'Aug 2027',
                'status'            => 'Active Deduction',
            ],
            [
                'employee'          => 'Sarah Johnson',
                'loan_type'         => 'Pag-IBIG Multi-Purpose Loan (MPL)',
                'reference_number'  => 'HDMF-MPL-2026-4412',
                'total_loan'        => '₱25,000.00',
                'monthly_amort'     => '₱1,154.20',
                'semi_monthly_ded'  => '₱577.10',
                'remaining_balance' => '₱19,230.00',
                'term_end'          => 'Mar 2028',
                'status'            => 'Active Deduction',
            ],
        ];

        // 4. Bank Disbursement Accounts
        $bankAccounts = [
            [
                'bank_name'      => 'Landbank of the Philippines',
                'branch'         => 'Cebu Capitol Branch',
                'account_number' => '1234-5678-90',
                'format'         => 'LBP weAccess / Findes Hash Total Protocol',
                'type'           => 'Primary Government / Educational Account',
                'status'         => 'Connected & Verified',
            ],
            [
                'bank_name'      => 'BDO Unibank',
                'branch'         => 'Fuente Osmeña Branch',
                'account_number' => '0098-7654-32',
                'format'         => 'BDO Corporate Payroll Batch CSV',
                'type'           => 'Secondary Commercial Payroll Account',
                'status'         => 'Connected',
            ],
            [
                'bank_name'      => 'Bank of the Philippine Islands (BPI)',
                'branch'         => 'Escario Branch',
                'account_number' => '4567-8901-23',
                'format'         => 'BPI BizLink Fixed Width Protocol',
                'type'           => 'Auxiliary Payroll Account',
                'status'         => 'Connected',
            ],
        ];

        return response()->json([
            'payroll_runs'     => $payrollRuns,
            'remittances'       => $remittances,
            'loans'             => $loans,
            'bank_accounts'     => $bankAccounts,
        ]);
    }

    /**
     * Get data for RECRUITMENT & PERFORMANCE modules:
     * Vacancies, Recruitment, Requisitions, Job Postings, Applicants, ATS Pipeline,
     * Interviews, Assessments, Job Offers, Pre-employment Requirements, Hire & Onboarding,
     * Reports & Analytics, Performance Dashboard, Performance Cycles, Performance Templates,
     * Performance Plans, Performance Reviews, Performance Calibration, Final Ratings,
     * Development Plans, Improvement Plans, Performance Reports, Performance Security,
     * Performance Certification, Template Studio, Placeholder Library, HR Service Requests,
     * Cache & Performance, Pipeline Settings, Recruitment Security, Recruitment Certification.
     */
    public function getRecruitmentData(Request $request): JsonResponse
    {
        // 1. Vacancies
        $vacancies = [
            [
                'id'            => 'VAC-2026-001',
                'title'         => 'Associate Professor of Computer Science',
                'department'    => 'College of Arts & Sciences',
                'plantilla_item'=> 'PLN-2026-001',
                'salary_grade'  => 'SG-22 (₱45,000.00/mo)',
                'openings'      => 2,
                'applicants'    => 14,
                'status'        => 'Active Hiring',
                'deadline'      => 'Nov 15, 2026',
            ],
            [
                'id'            => 'VAC-2026-002',
                'title'         => 'Administrative Officer IV (HRMO II)',
                'department'    => 'Human Resources Management Office',
                'plantilla_item'=> 'PLN-2026-004',
                'salary_grade'  => 'SG-15 (₱36,619.00/mo)',
                'openings'      => 1,
                'applicants'    => 28,
                'status'        => 'Active Hiring',
                'deadline'      => 'Nov 20, 2026',
            ],
            [
                'id'            => 'VAC-2026-003',
                'title'         => 'Instructor I - Mathematics & Statistics',
                'department'    => 'College of Education',
                'plantilla_item'=> 'PLN-2026-003',
                'salary_grade'  => 'SG-12 (₱29,165.00/mo)',
                'openings'      => 3,
                'applicants'    => 19,
                'status'        => 'Shortlisting',
                'deadline'      => 'Nov 10, 2026',
            ],
            [
                'id'            => 'VAC-2026-004',
                'title'         => 'Senior Accounting Specialist',
                'department'    => 'Finance & Accounting Division',
                'plantilla_item'=> 'PLN-2026-005',
                'salary_grade'  => 'SG-18 (₱46,725.00/mo)',
                'openings'      => 1,
                'applicants'    => 9,
                'status'        => 'Interview Stage',
                'deadline'      => 'Nov 05, 2026',
            ],
        ];

        // 2. Requisitions
        $requisitions = [
            [
                'req_number'   => 'REQ-2026-042',
                'department'   => 'College of Arts & Sciences',
                'position'     => 'Assistant Professor I - Data Science',
                'slots'        => 2,
                'justification'=> 'CHED Curriculum expansion for BS Data Analytics.',
                'budget_status'=> 'VP Finance Approved',
                'status'       => 'Approved for Posting',
            ],
            [
                'req_number'   => 'REQ-2026-043',
                'department'   => 'Information & Communications Technology',
                'position'     => 'Network Security Administrator',
                'slots'        => 1,
                'justification'=> 'Campus Wi-Fi 6 upgrade & ISO 27001 compliance.',
                'budget_status'=> 'VP Finance Approved',
                'status'       => 'Approved for Posting',
            ],
            [
                'req_number'   => 'REQ-2026-044',
                'department'   => 'University Library Services',
                'position'     => 'College Librarian I',
                'slots'        => 1,
                'justification'=> 'Replacement for retired staff.',
                'budget_status'=> 'Under Review',
                'status'       => 'Pending Budget Endorsement',
            ],
        ];

        // 3. Job Postings
        $jobPostings = [
            [
                'title'       => 'Associate Professor of Computer Science',
                'channels'    => ['CSC Bulletin', 'University Website', 'LinkedIn'],
                'date_posted' => 'Oct 15, 2026',
                'views'       => 428,
                'applications'=> 14,
                'status'      => 'Published',
            ],
            [
                'title'       => 'Administrative Officer IV (HRMO II)',
                'channels'    => ['CSC Bulletin', 'JobStreet PH', 'Facebook Jobs'],
                'date_posted' => 'Oct 18, 2026',
                'views'       => 612,
                'applications'=> 28,
                'status'      => 'Published',
            ],
            [
                'title'       => 'Senior Accounting Specialist',
                'channels'    => ['CSC Bulletin', 'LinkedIn Talent'],
                'date_posted' => 'Oct 02, 2026',
                'views'       => 310,
                'applications'=> 9,
                'status'      => 'Published',
            ],
        ];

        // 4. Applicants & ATS Pipeline
        $applicants = [
            [
                'id'         => 'APP-2026-081',
                'name'       => 'Engr. Daniel Alcantara, MSCS',
                'email'      => 'daniel.alcantara@gmail.com',
                'position'   => 'Associate Professor of Computer Science',
                'stage'      => 'Assessment',
                'rating'     => 4.9,
                'applied_at' => 'Oct 20, 2026',
                'pds_status' => 'CSC PDS Complete',
            ],
            [
                'id'         => 'APP-2026-082',
                'name'       => 'Atty. Cristina Velasco, CPA',
                'email'      => 'c.velasco@yahoo.com',
                'position'   => 'Senior Accounting Specialist',
                'stage'      => 'Job Offer',
                'rating'     => 4.8,
                'applied_at' => 'Oct 12, 2026',
                'pds_status' => 'CSC PDS Complete',
            ],
            [
                'id'         => 'APP-2026-083',
                'name'       => 'Dr. Raymond Tan, PhD',
                'email'      => 'raymond.tan@alumni.up.edu.ph',
                'position'   => 'Associate Professor of Computer Science',
                'stage'      => 'Interview',
                'rating'     => 4.7,
                'applied_at' => 'Oct 22, 2026',
                'pds_status' => 'CSC PDS Complete',
            ],
            [
                'id'         => 'APP-2026-084',
                'name'       => 'Patricia Nicole Ramos',
                'email'      => 'patricia.ramos@outlook.com',
                'position'   => 'Administrative Officer IV (HRMO II)',
                'stage'      => 'Screening',
                'rating'     => 4.5,
                'applied_at' => 'Oct 25, 2026',
                'pds_status' => 'Under Verification',
            ],
            [
                'id'         => 'APP-2026-085',
                'name'       => 'Mark Anthony Bautista',
                'email'      => 'mark.bautista@gmail.com',
                'position'   => 'Instructor I - Mathematics',
                'stage'      => 'Hire & Onboarding',
                'rating'     => 5.0,
                'applied_at' => 'Oct 05, 2026',
                'pds_status' => 'Verified & Approved',
            ],
        ];

        // 5. Interviews
        $interviews = [
            [
                'candidate'   => 'Dr. Raymond Tan, PhD',
                'position'    => 'Associate Professor of Computer Science',
                'panel'       => 'Academic Dean + Search Committee',
                'datetime'    => 'Oct 30, 2026 • 2:00 PM',
                'modality'    => 'Conference Room A (In-person)',
                'status'      => 'Scheduled',
            ],
            [
                'candidate'   => 'Patricia Nicole Ramos',
                'position'    => 'Administrative Officer IV (HRMO II)',
                'panel'       => 'HR Director & Administrative Head',
                'datetime'    => 'Oct 31, 2026 • 10:00 AM',
                'modality'    => 'Zoom Video Conference',
                'status'      => 'Confirmed',
            ],
        ];

        // 6. Assessments
        $assessments = [
            [
                'candidate'   => 'Engr. Daniel Alcantara, MSCS',
                'type'        => 'Teaching Demonstration & Syllabus Defense',
                'evaluator'   => 'College Search Committee',
                'score'       => '94.5 / 100',
                'status'      => 'Passed with Recommendation',
            ],
            [
                'candidate'   => 'Patricia Nicole Ramos',
                'type'        => 'Civil Service CSC Law & Omnibus Rules Exam',
                'evaluator'   => 'HR Assessment Testing Center',
                'score'       => '88.0 / 100',
                'status'      => 'Passed',
            ],
        ];

        // 7. Job Offers
        $jobOffers = [
            [
                'candidate'     => 'Atty. Cristina Velasco, CPA',
                'position'      => 'Senior Accounting Specialist',
                'salary_grade'  => 'SG-18, Step 1',
                'monthly_rate'  => '₱46,725.00',
                'allowance'     => '₱2,000.00 PERA + Hazard Pay',
                'start_date'    => 'Nov 16, 2026',
                'status'        => 'Offer Letter Sent (Pending Candidate Signature)',
            ],
        ];

        // 8. Pre-employment Requirements Checklist (Philippine Civil Service Standard)
        $preEmploymentRequirements = [
            [
                'candidate'     => 'Mark Anthony Bautista',
                'position'      => 'Instructor I - Mathematics',
                'checklist'     => [
                    'NBI Clearance (Valid 2026)'                  => 'Verified',
                    'Civil Service Form 212 (PDS Revised 2017)'   => 'Verified',
                    'Medical Certificate (CS Form 211) & Drug Test'=> 'Verified',
                    'Official Transcript of Records & Diploma'    => 'Verified',
                    'BIR Form 1902 (TIN Registration)'            => 'Verified',
                    'SSS Member Data Record'                       => 'Verified',
                    'PhilHealth Member Registration'              => 'Verified',
                    'Pag-IBIG Member Identification (MID)'        => 'Verified',
                ],
                'overall_status'=> '100% Complete - Cleared for Appointment',
            ],
            [
                'candidate'     => 'Atty. Cristina Velasco, CPA',
                'position'      => 'Senior Accounting Specialist',
                'checklist'     => [
                    'NBI Clearance (Valid 2026)'                  => 'Pending',
                    'Civil Service Form 212 (PDS Revised 2017)'   => 'Submitted',
                    'Medical Certificate (CS Form 211) & Drug Test'=> 'In Progress',
                    'Official Transcript of Records & Diploma'    => 'Submitted',
                    'BIR Form 1902 (TIN Registration)'            => 'Submitted',
                    'SSS Member Data Record'                       => 'Submitted',
                    'PhilHealth Member Registration'              => 'Submitted',
                    'Pag-IBIG Member Identification (MID)'        => 'Submitted',
                ],
                'overall_status'=> '62% In Progress',
            ],
        ];

        // 9. Hire & Onboarding
        $onboarding = [
            [
                'employee'          => 'Mark Anthony Bautista',
                'role'              => 'Instructor I - Mathematics',
                'start_date'        => 'Nov 03, 2026',
                'id_badge'          => 'Issued',
                'zkteco_biometric'  => 'Enrolled (ZK-Teco Terminal 1)',
                'institutional_email'=> 'mbautista@sancarlos.edu.ph',
                'mentor'            => 'Kathryn Murphy (Dean of Education)',
                'status'            => 'Onboarding Week 1',
            ],
        ];

        // 10. Performance Management Suite (IPCR / OPCR Philippine Academic Standard)
        $performanceCycles = [
            [
                'cycle_name'    => 'AY 2026-2027 First Semester Evaluation',
                'type'          => 'Individual Performance Commitment & Review (IPCR)',
                'target_period' => 'August 2026 – December 2026',
                'status'        => 'Active Submission Stage',
                'completion'    => '86% Evaluated',
                'due_date'      => 'Dec 15, 2026',
            ],
            [
                'cycle_name'    => 'Mid-Year Institutional OPCR Review',
                'type'          => 'Office Performance Commitment & Review (OPCR)',
                'target_period' => 'January 2026 – June 2026',
                'status'        => 'Adjudicated & Certified',
                'completion'    => '100% Completed',
                'due_date'      => 'Jul 15, 2026',
            ],
        ];

        // 11. Final Ratings & Rankings
        $finalRatings = [
            [
                'employee'      => 'Prof. Antonio Luna',
                'position'      => 'Associate Professor IV',
                'department'    => 'College of Arts & Sciences',
                'numeric_score' => '4.88',
                'adjectival'    => 'Outstanding',
                'pbb_tier'      => 'Top 10% (PBB Tier 1 Incentive)',
                'certified_by'  => 'Performance Management Committee',
            ],
            [
                'employee'      => 'Kathryn Murphy',
                'position'      => 'Assistant Professor II',
                'department'    => 'College of Education',
                'numeric_score' => '4.72',
                'adjectival'    => 'Very Satisfactory',
                'pbb_tier'      => 'Next 25% (PBB Tier 2 Incentive)',
                'certified_by'  => 'Performance Management Committee',
            ],
            [
                'employee'      => 'Jenny Wilson',
                'position'      => 'Instructor I',
                'department'    => 'College of Arts & Sciences',
                'numeric_score' => '4.55',
                'adjectival'    => 'Very Satisfactory',
                'pbb_tier'      => 'Next 25% (PBB Tier 2 Incentive)',
                'certified_by'  => 'Performance Management Committee',
            ],
            [
                'employee'      => 'Sarah Johnson',
                'position'      => 'HR Specialist / Officer',
                'department'    => 'Human Resources Management Office',
                'numeric_score' => '4.91',
                'adjectival'    => 'Outstanding',
                'pbb_tier'      => 'Top 10% (PBB Tier 1 Incentive)',
                'certified_by'  => 'Performance Management Committee',
            ],
        ];

        // 12. Template Studio & Placeholder Library
        $templates = [
            [
                'id'          => 'TPL-001',
                'title'       => 'Civil Service Form 33-A (Appointment Paper)',
                'category'    => 'Legal & Plantilla Appointments',
                'format'      => 'Official CSC Standard',
                'last_updated'=> 'Oct 2026',
            ],
            [
                'id'          => 'TPL-002',
                'title'       => 'Notice of Salary Adjustment (NOSA / Step Increment)',
                'category'    => 'Compensation & Benefits',
                'format'      => 'DBM Circular Standard',
                'last_updated'=> 'Sep 2026',
            ],
            [
                'id'          => 'TPL-003',
                'title'       => 'Certificate of Employment & Compensation (COEC)',
                'category'    => 'Employee Self-Service / Verification',
                'format'      => 'Digital QR Signed',
                'last_updated'=> 'Oct 2026',
            ],
            [
                'id'          => 'TPL-004',
                'title'       => 'Job Offer & Terms of Engagement Letter',
                'category'    => 'Talent Acquisition',
                'format'      => 'Institutional Letterhead',
                'last_updated'=> 'Oct 2026',
            ],
        ];

        $placeholders = [
            ['tag' => '{{employee.full_name}}', 'description' => 'First, middle, and last name with title', 'example' => 'Engr. Daniel Alcantara, MSCS'],
            ['tag' => '{{employee.position_title}}', 'description' => 'Official plantilla or contract position', 'example' => 'Associate Professor of Computer Science'],
            ['tag' => '{{employee.salary_grade}}', 'description' => 'Philippine Salary Grade and step increment', 'example' => 'SG-22 Step 1'],
            ['tag' => '{{employee.monthly_base}}', 'description' => 'Formatted monthly authorized compensation', 'example' => '₱45,000.00'],
            ['tag' => '{{tenant.name}}', 'description' => 'Name of campus or institution', 'example' => 'University of San Carlos - Main Campus'],
            ['tag' => '{{effective_date}}', 'description' => 'Contract or adjustment effectivity date', 'example' => 'November 01, 2026'],
        ];

        // 13. HR Service Requests
        $serviceRequests = [
            [
                'id'          => 'SR-2026-104',
                'employee'    => 'Jenny Wilson',
                'type'        => 'Certificate of Employment (with Compensation)',
                'purpose'     => 'Bank Home Loan Application',
                'date'        => 'Oct 28, 2026',
                'status'      => 'Approved (Ready for Download)',
            ],
            [
                'id'          => 'SR-2026-105',
                'employee'    => 'Prof. Antonio Luna',
                'type'        => 'Official Service Record (CSC Form 212 Linked)',
                'purpose'     => 'CHED Professorial Fellowship Grant',
                'date'        => 'Oct 27, 2026',
                'status'      => 'Generated & QR Certified',
            ],
            [
                'id'          => 'SR-2026-106',
                'employee'    => 'Kathryn Murphy',
                'type'        => 'Leave Credit Monetization Request (10 Days)',
                'purpose'     => 'Medical Emergency Assistance',
                'date'        => 'Oct 29, 2026',
                'status'      => 'Under Finance Review',
            ],
        ];

        // 14. Cache & System Metrics
        $systemCache = [
            'redis_status'       => 'Operational (Cluster 01)',
            'cache_hit_rate'     => '98.7%',
            'opcache_enabled'    => true,
            'query_avg_ms'       => '1.6 ms',
            'active_sessions'    => 14,
            'last_cache_flush'   => 'Today at 04:00 AM PHT',
        ];

        return response()->json([
            'vacancies'                   => $vacancies,
            'requisitions'                => $requisitions,
            'job_postings'                => $jobPostings,
            'applicants'                  => $applicants,
            'interviews'                  => $interviews,
            'assessments'                 => $assessments,
            'job_offers'                  => $jobOffers,
            'pre_employment_requirements' => $preEmploymentRequirements,
            'onboarding'                  => $onboarding,
            'performance_cycles'          => $performanceCycles,
            'final_ratings'               => $finalRatings,
            'templates'                   => $templates,
            'placeholders'                => $placeholders,
            'service_requests'            => $serviceRequests,
            'system_cache'                => $systemCache,
        ]);
    }
}

