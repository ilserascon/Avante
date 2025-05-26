<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        $userRole = auth()->user()->role->nombre ?? '';
        if (!in_array($userRole, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        return $next($request);
    }
}