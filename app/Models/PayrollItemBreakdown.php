<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItemBreakdown extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    public function getAmountAttribute($value): string
    {
        return number_format((float)$value, 4, '.', '');
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }
}

