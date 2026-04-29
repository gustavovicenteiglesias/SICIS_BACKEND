<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->hasPermission($permission)) {
            abort(403, 'No tenes permisos para realizar esta accion.');
        }

        return $next($request);
    }
}
