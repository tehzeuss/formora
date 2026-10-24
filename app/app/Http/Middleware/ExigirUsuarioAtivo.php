<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExigirUsuarioAtivo
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()?->ativo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('aviso', 'Seu acesso está inativo. Procure o administrador.');
        }

        return $next($request);
    }
}
