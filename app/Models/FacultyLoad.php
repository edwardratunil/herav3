<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyLoad extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'lecture_units'          => 'string',
        'lab_contact_hours'      => 'string',
        'lab_multiplier'         => 'string',
        'credited_load_units'    => 'string',
        'overload_rate_per_unit' => 'string',
        'is_approved_by_dean'    => 'boolean',
        'approved_at'            => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}

