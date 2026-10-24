<?php

namespace App\Http\Middleware;

use App\Models\Formulario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcessoFormularioCompartilhado
{
    public function handle(Request $request, Closure $next)
    {
        $formulario = Formulario::where('token_compartilhamento', $request->route('token'))->firstOrFail();
        abort_if($formulario->situacao === 'rascunho', 404);
        if ($formulario->situacao === 'encerrado') {
            abort_if($request->routeIs('compartilhamento.imagem'), 404);

            return response()->view('formularios.compartilhamento.encerrado', ['form' => $formulario], 200)->header('Cache-Control', 'private, no-store');
        }
        abort_unless($formulario->situacao === 'publicado', 404);
        if ($formulario->prazoEncerrado()) {
            abort_if($request->routeIs('compartilhamento.imagem'), 404);

            return response()->view('formularios.compartilhamento.prazo-encerrado', ['form' => $formulario], 200)->header('Cache-Control', 'private, no-store');
        }
        if ($formulario->exigeLogin() && (! $request->user() || ! $request->user()->ativo)) {
            if ($request->user()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            $request->session()->put('url.intended', route('compartilhamento.exibir', $formulario->token_compartilhamento));

            return redirect()->route('login')->with('aviso', 'Entre com seu e-mail e senha para acessar este formulário.');
        }
        $request->attributes->set('formulario_compartilhado', $formulario);

        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
