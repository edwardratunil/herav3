<?php

namespace App\Domain\Leave;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Leave Credit Management Engine
 * Implements Philippine Leave Policies (DOLE SIL, CSC Omnibus 15-day VL/SL, Magna Carta).
 */
class LeaveCreditManager
{
    public const SCALE = 4;

    /**
     * Allocate or initialize annual leave credits for an employee.
     */
    public function allocateAnnualCredits(
        Employee $employee,
        LeaveType $leaveType,
        int $year,
        ?string $customDays = null
    ): LeaveBalance {
        $days = $customDays ?? (string)$leaveType->days_per_year;

        return LeaveBalance::updateOrCreate(
            [
                'tenant_id'     => $employee->tenant_id,
                'employee_id'   => $employee->id,
                'leave_type_id' => $leaveType->id,
                'year'          => $year,
            ],
            [
                'allocated_days' => $days,
                'used_days'      => '0.00',
                'pending_days'   => '0.00',
                'remaining_days' => $days,
            ]
        );
    }

    /**
     * File a new leave request.
     */
    public function submitLeaveRequest(
        Employee $employee,
        LeaveType $leaveType,
        string $startDate,
        string $endDate,
        string $daysCount,
        string $reason
    ): LeaveRequest {
        $year = Carbon::parse($startDate)->year;
        $balance = LeaveBalance::where('tenant_id', $employee->tenant_id)
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $year)
            ->first();

        if ($leaveType->is_paid && (!$balance || bccomp((string)$balance->remaining_days, $daysCount, self::SCALE) < 0)) {
            $available = $balance ? $balance->remaining_days : '0.00';
            throw new RuntimeException("Insufficient leave credits. Requested: {$daysCount} days, Available: {$available} days.");
        }

        return DB::transaction(function () use ($employee, $leaveType, $startDate, $endDate, $daysCount, $reason, $balance) {
            $request = LeaveRequest::create([
                'tenant_id'     => $employee->tenant_id,
                'employee_id'   => $employee->id,
                'leave_type_id' => $leaveType->id,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'days_count'    => $daysCount,
                'reason'        => $reason,
                'status'        => 'pending',
            ]);

            if ($balance) {
                $newPending = bcadd((string)$balance->pending_days, $daysCount, self::SCALE);
                $balance->update(['pending_days' => $newPending]);
            }

            return $request;
        });
    }

    /**
     * Tier 1 Approval: Department Head / Academic Dean
     */
    public function approveByHead(LeaveRequest $request, string $headUserId): LeaveRequest
    {
        if ($request->status !== 'pending') {
            throw new RuntimeException("Leave request is not in pending status.");
        }

        $request->update([
            'status'              => 'approved_by_head',
            'approved_by_head_id' => $headUserId,
            'approved_by_head_at' => now(),
        ]);

        return $request;
    }

    /**
     * Tier 2 Approval: HR Officer (Final Authorization & Balance Deduction)
     */
    public function approveByHr(LeaveRequest $request, string $hrUserId): LeaveRequest
    {
        if (!in_array($request->status, ['pending', 'approved_by_head'])) {
            throw new RuntimeException("Leave request cannot be finalized in current status ({$request->status}).");
        }

        return DB::transaction(function () use ($request, $hrUserId) {
            $year = Carbon::parse($request->start_date)->year;
            $balance = LeaveBalance::where('tenant_id', $request->tenant_id)
                ->where('employee_id', $request->employee_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('year', $year)
                ->first();

            if ($balance) {
                $requestedDays = (string)$request->days_count;
                $newUsed = number_format((float)bcadd((string)$balance->used_days, $requestedDays, self::SCALE), 2, '.', '');
                $newRemaining = number_format((float)bcsub((string)$balance->remaining_days, $requestedDays, self::SCALE), 2, '.', '');
                $rawPending = bcsub((string)$balance->pending_days, $requestedDays, self::SCALE);
                $newPending = (bccomp($rawPending, '0.0000', self::SCALE) < 0) ? '0.00' : number_format((float)$rawPending, 2, '.', '');

                $balance->update([
                    'used_days'      => $newUsed,
                    'remaining_days' => $newRemaining,
                    'pending_days'   => $newPending,
                ]);
            }

            $request->update([
                'status'            => 'approved_by_hr',
                'approved_by_hr_id' => $hrUserId,
                'approved_by_hr_at' => now(),
            ]);

            // Sync with Attendance Logs: mark days as on_leave
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->startOfDay();
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                AttendanceLog::updateOrCreate(
                    [
                        'tenant_id'   => $request->tenant_id,
                        'employee_id' => $request->employee_id,
                        'log_date'    => $date->toDateString(),
                    ],
                    [
                        'status'      => 'on_leave',
                        'source'      => 'approved_leave_system',
                        'is_verified' => true,
                    ]
                );
            }

            return $request;
        });
    }
}
