<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentContract extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'base_rate'            => 'string',
        'de_minimis_allowance' => 'string',
        'taxable_allowance'    => 'string',
        'effective_date'       => 'date',
        'expiry_date'          => 'date',
        'is_active'            => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

