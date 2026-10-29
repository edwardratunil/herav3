<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'is_system'   => 'boolean',
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'id');
    }
}

