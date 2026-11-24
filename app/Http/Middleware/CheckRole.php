<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk ' . strtoupper(implode(' atau ', $roles)) . '.');
        }

        return $next($request);
    }
}