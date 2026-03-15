<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não tiver o token '*' (admin), bloqueia
        if (! $request->user() || ! $request->user()->tokenCan('*')) {
            abort(403, 'Ação não autorizada.');
        }

        return $next($request);
    }
}


