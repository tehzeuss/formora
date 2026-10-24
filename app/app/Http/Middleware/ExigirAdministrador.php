<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExigirAdministrador
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->ativo && $request->user()->eAdministrador(), 403);

        return $next($request);
    }
}
