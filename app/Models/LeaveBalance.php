<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
    ];

    public function getAllocatedDaysAttribute($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function getUsedDaysAttribute($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function getPendingDaysAttribute($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function getRemainingDaysAttribute($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
