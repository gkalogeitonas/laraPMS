<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(session()->has('active_tenant_id')) {
            $builder->where('tenant_id', session()->get('active_tenant_id'));
        } else {
            // Add a condition that will always be false to ensure no results are returned
            $builder->whereRaw('1 = 0');
        }
    }
}
