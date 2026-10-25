<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Trava de reentrância para evitar recursão infinita ao resolver o usuário autenticado.
     */
    protected static bool $resolvingTenantId = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = static::getTenantId();

        // Se houver um tenant ativo no contexto (sessão ou usuário autenticado)
        if ($tenantId && $tenantId !== 'all') {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }

    /**
     * Obtém o ID do tenant ativo atual no contexto da requisição com proteção anti-recursão.
     */
    public static function getTenantId(): mixed
    {
        // 1. Se já existir tenant_id na sessão, usa diretamente (máxima performance, sem chamar Auth)
        if (session()->has('tenant_id')) {
            return session('tenant_id');
        }

        // 2. Proteção contra recursão infinita se o Auth::user() disparar query no Model User
        if (static::$resolvingTenantId) {
            return null;
        }

        static::$resolvingTenantId = true;

        try {
            $user = request()->user() ?? (Auth::hasUser() ? Auth::user() : null);
            if ($user && $user->tenant_id) {
                return $user->tenant_id;
            }
        } catch (\Throwable $e) {
            // Em caso de qualquer exceção durante a inicialização do auth, ignora e retorna null
            return null;
        } finally {
            static::$resolvingTenantId = false;
        }

        return null;
    }
}
