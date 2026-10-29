<?php

namespace App\Models;

use App\Domain\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    use HasUuids, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'basic_earned'                   => 'string',
        'faculty_overload_pay'           => 'string',
        'allowances_taxable'             => 'string',
        'allowances_non_taxable'         => 'string',
        'gross_pay'                      => 'string',
        'sss_ee'                         => 'string',
        'gsis_ee'                        => 'string',
        'philhealth_ee'                  => 'string',
        'pagibig_ee'                     => 'string',
        'withholding_tax'                => 'string',
        'sss_er'                         => 'string',
        'gsis_er'                        => 'string',
        'philhealth_er'                  => 'string',
        'pagibig_er'                     => 'string',
        'ec_er'                          => 'string',
        'tardiness_undertime_deduction'  => 'string',
        'loan_deductions'                => 'string',
        'other_deductions'               => 'string',
        'total_deductions'               => 'string',
        'net_pay'                        => 'string',
        'calculation_snapshot'           => 'array',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function breakdowns(): HasMany
    {
        return $this->hasMany(PayrollItemBreakdown::class);
    }
}

