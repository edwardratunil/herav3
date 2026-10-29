<?php

namespace Tests\Unit;

use App\Domain\Attendance\AttendanceDtrEngine;
use App\Domain\Attendance\ZkTeCoAttendanceParser;
use App\Domain\Leave\LeaveCreditManager;
use App\Domain\Tenancy\TenantContext;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Shift;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceAndLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_zkteco_biometric_parser_aggregates_punches(): void
    {
        $parser = new ZkTeCoAttendanceParser();

        // Sample ZKTeco punch data: multiple punches on the same day for employee 101
        $rawDump = "101\t2026-10-01 07:55:12\t0\n" .
                   "101\t2026-10-01 12:02:44\t1\n" .
                   "101\t2026-10-01 12:58:10\t0\n" .
                   "101\t2026-10-01 17:05:30\t1\n" .
                   "102\t2026-10-01 08:25:00\t0\n";

        $result = $parser->parse($rawDump);

        $this->assertArrayHasKey('101', $result);
        $this->assertArrayHasKey('2026-10-01', $result['101']);
        $this->assertEquals('2026-10-01 07:55:12', $result['101']['2026-10-01']['time_in']);
        $this->assertEquals('2026-10-01 17:05:30', $result['101']['2026-10-01']['time_out']);
        $this->assertEquals(4, $result['101']['2026-10-01']['punch_count']);

        $this->assertArrayHasKey('102', $result);
        $this->assertEquals('2026-10-01 08:25:00', $result['102']['2026-10-01']['time_in']);
    }

    public function test_tardiness_and_undertime_evaluation(): void
    {
        $engine = new AttendanceDtrEngine();

        // Shift: 08:00 to 17:00, 15 min grace period
        $shift = new Shift([
            'start_time'           => '08:00:00',
            'end_time'             => '17:00:00',
            'grace_period_minutes' => 15,
        ]);

        // Case A: Within grace period (08:10) -> Tardiness = 0
        $evalA = $engine->evaluateDayPunch(
            Carbon::parse('2026-10-01 08:10:00'),
            Carbon::parse('2026-10-01 17:05:00'),
            $shift
        );
        $this->assertEquals(0, $evalA['tardiness_minutes']);
        $this->assertEquals(0, $evalA['undertime_minutes']);

        // Case B: Past grace period (08:35) -> Tardiness = 35 minutes from 08:00
        // Undertime: Leaves at 16:30 -> Undertime = 30 minutes from 17:00
        $evalB = $engine->evaluateDayPunch(
            Carbon::parse('2026-10-01 08:35:00'),
            Carbon::parse('2026-10-01 16:30:00'),
            $shift
        );
        $this->assertEquals(35, $evalB['tardiness_minutes']);
        $this->assertEquals(30, $evalB['undertime_minutes']);
    }

    public function test_philippine_minute_rate_and_attendance_deduction(): void
    {
        $engine = new AttendanceDtrEngine();

        // Monthly salary: ₱31,300.00 | Factor 313 (6 days/week)
        // Annual = ₱375,600.00
        // Daily = ₱375,600 / 313 = ₱1,200.00
        // Hourly = ₱1,200 / 8 = ₱150.00
        // Minute = ₱150 / 60 = ₱2.5000 per minute
        $rates = $engine->computeRates('31300.0000', AttendanceDtrEngine::FACTOR_6_DAYS);
        $this->assertEquals('1200.0000', $rates['daily_rate']);
        $this->assertEquals('150.0000', $rates['hourly_rate']);
        $this->assertEquals('2.5000', $rates['minute_rate']);

        // 35 minutes late + 1.0 day unpaid absence:
        // Tardy Deduction: 35 * ₱2.50 = ₱87.50
        // Absence Deduction: 1 * ₱1,200.00 = ₱1,200.00
        // Total Attendance Deduction: ₱1,287.50
        $deduction = $engine->calculateAttendanceDeduction(
            '31300.0000',
            35,
            0,
            '1.0000',
            AttendanceDtrEngine::FACTOR_6_DAYS
        );

        $this->assertEquals('87.5000', $deduction['tardiness_deduction']);
        $this->assertEquals('1200.0000', $deduction['absence_deduction']);
        $this->assertEquals('1287.5000', $deduction['total_attendance_deduction']);
    }

    public function test_leave_credit_allocation_and_twotier_approval(): void
    {
        $tenant = Tenant::create([
            'code'             => 'SCH-LEAVE',
            'name'             => 'Ateneo de Naga',
            'institution_type' => 'private_hei',
        ]);
        TenantContext::setTenantId($tenant->id);

        $employee = Employee::create([
            'employee_number' => 'FAC-777',
            'first_name'      => 'Clara',
            'last_name'       => 'Batac',
            'date_hired'      => '2023-01-01',
        ]);

        $leaveType = LeaveType::create([
            'name'          => 'Sick Leave',
            'code'          => 'SL',
            'is_paid'       => true,
            'days_per_year' => '15.00',
        ]);

        $manager = new LeaveCreditManager();

        // 1. Allocate 15.00 days for Year 2026
        $balance = $manager->allocateAnnualCredits($employee, $leaveType, 2026);
        $this->assertEquals('15.00', $balance->allocated_days);
        $this->assertEquals('15.00', $balance->remaining_days);

        // 2. File a 2-day Sick Leave request
        $request = $manager->submitLeaveRequest(
            $employee,
            $leaveType,
            '2026-10-05',
            '2026-10-06',
            '2.00',
            'Acute Bronchitis'
        );

        $this->assertEquals('pending', $request->status);
        $this->assertEquals('2.00', $balance->fresh()->pending_days);

        // 3. Tier 1 Approval: Department Head
        $headId = '00000000-0000-0000-0000-000000000002';
        $manager->approveByHead($request, $headId);
        $this->assertEquals('approved_by_head', $request->fresh()->status);

        // 4. Tier 2 Approval: HR Officer (Deducts balance & syncs with DTR)
        $hrId = '00000000-0000-0000-0000-000000000003';
        $manager->approveByHr($request, $hrId);

        $freshReq = $request->fresh();
        $this->assertEquals('approved_by_hr', $freshReq->status);
        $this->assertTrue($freshReq->isFullyApproved());

        $freshBal = $balance->fresh();
        $this->assertEquals('2.00', $freshBal->used_days);
        $this->assertEquals('13.00', $freshBal->remaining_days);
        $this->assertEquals('0.00', $freshBal->pending_days);

        // Verify Attendance Logs are automatically created with status 'on_leave'
        $log1 = $employee->attendanceLogs()->whereDate('log_date', '2026-10-05')->first();
        $log2 = $employee->attendanceLogs()->whereDate('log_date', '2026-10-06')->first();
        $this->assertNotNull($log1);
        $this->assertEquals('on_leave', $log1->status);
        $this->assertNotNull($log2);
        $this->assertEquals('on_leave', $log2->status);

        TenantContext::clear();
    }
}
