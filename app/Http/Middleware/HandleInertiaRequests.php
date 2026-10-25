<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $currentTenantId = \App\Scopes\TenantScope::getTenantId();
        $currentTenant = null;

        if ($currentTenantId && $currentTenantId !== 'all') {
            $currentTenant = \App\Models\Tenant::find($currentTenantId);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), [
                    'tenant' => $user->tenant,
                    'is_super_admin' => $user->isSuperAdmin(),
                ]) : null,
                'active_tenant' => $currentTenant,
                'active_tenant_id' => $currentTenantId,
            ],
            'available_tenants' => fn () => ($user && $user->isSuperAdmin())
                ? \App\Models\Tenant::select('id', 'nome', 'slug', 'ativo')->get()
                : [],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
