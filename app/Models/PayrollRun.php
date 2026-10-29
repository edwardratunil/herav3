<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'period_start'                  => 'date',
        'period_end'                    => 'date',
        'payout_date'                   => 'date',
        'prepared_at'                   => 'datetime',
        'approved_at'                   => 'datetime',
        'total_gross_pay'               => 'string',
        'total_deductions'              => 'string',
        'total_net_pay'                 => 'string',
        'total_employer_contributions'  => 'string',
        'employee_count'                => 'integer',
        'statutory_versions_used'       => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['approved', 'locked']);
    }
}

