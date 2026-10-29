<?php

namespace App\Domain\Tenancy\Scopes;

use App\Domain\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * TenantScope ensures all queries against tenant-bound entities automatically include
 * WHERE tenant_id = <current_tenant_id>.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (TenantContext::hasTenant()) {
            $builder->where($model->qualifyColumn('tenant_id'), TenantContext::getTenantId());
        }
    }
}

