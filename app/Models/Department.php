<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}

