<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'days_count'           => 'string',
        'approved_by_head_at'  => 'datetime',
        'approved_by_hr_at'    => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function isFullyApproved(): bool
    {
        return $this->status === 'approved_by_hr';
    }
}

