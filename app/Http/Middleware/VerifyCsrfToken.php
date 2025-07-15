<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // Adicione aqui rotas a serem ignoradas, se necessário
    ];

    public function handle($request, Closure $next)
    {
        if (app()->environment('testing')) {
            return $next($request);
        }
        return parent::handle($request, $next);
    }
} 