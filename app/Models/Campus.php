<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'is_main_campus' => 'boolean',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}

