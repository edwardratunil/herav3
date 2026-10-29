<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasUuids, SoftDeletes, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'tin_encrypted'           => 'encrypted',
        'sss_no_encrypted'        => 'encrypted',
        'gsis_no_encrypted'       => 'encrypted',
        'philhealth_no_encrypted' => 'encrypted',
        'pagibig_no_encrypted'    => 'encrypted',
        'bank_account_encrypted'  => 'encrypted',
        'is_faculty'              => 'boolean',
        'date_hired'              => 'date',
        'date_separated'          => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmploymentContract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(EmploymentContract::class)->where('is_active', true)->latestOfMany();
    }

    public function facultyLoads(): HasMany
    {
        return $this->hasMany(FacultyLoad::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getFullNameAttribute(): string
    {
        $name = "{$this->last_name}, {$this->first_name}";
        if ($this->middle_name) {
            $name .= ' ' . substr($this->middle_name, 0, 1) . '.';
        }
        if ($this->suffix) {
            $name .= " {$this->suffix}";
        }
        return $name;
    }
}

