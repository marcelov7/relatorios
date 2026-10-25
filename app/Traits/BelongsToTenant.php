<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait to add the global tenant scope and auto-assign tenant_id.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $tenantId = TenantScope::getTenantId();
                if ($tenantId && $tenantId !== 'all') {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    /**
     * Relacionamento com o Tenant/Empresa.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Scope local para ignorar a restrição de tenant e buscar em todos os registros.
     */
    public function scopeWithoutTenancy(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
