<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Se a sessão ainda não possui tenant_id definido, inicializa com o do usuário
            if (!session()->has('tenant_id') && $user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            }
        }

        return $next($request);
    }
}
