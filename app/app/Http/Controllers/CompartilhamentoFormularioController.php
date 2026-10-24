<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompartilhamentoFormularioController extends Controller
{
    public function exibir(Request $request)
    {
        $form = $request->attributes->get('formulario_compartilhado');
        $form->load('perguntas');

        if ($request->session()->get('envio_confirmado') === $form->id) {
            return response()->view('formularios.compartilhamento.confirmacao', compact('form'));
        }
        if ($form->exigeLogin() && \App\Models\EnvioFormulario::usuarioJaRespondeu($form, $request->user())) {
            return response()->view('formularios.compartilhamento.ja-respondido', compact('form'));
        }
        $chaveEnvio = (string) \Illuminate\Support\Str::uuid();
        $chaves = (array) $request->session()->get('chaves_envio.'.$form->id, []);
        $chaves[] = $chaveEnvio;
        $request->session()->put('chaves_envio.'.$form->id, array_slice($chaves, -10));

        return response()->view('formularios.compartilhamento.exibir', compact('form', 'chaveEnvio'));
    }

    public function imagem(Request $request, string $token, ?string $pergunta = null)
    {
        $form = $request->attributes->get('formulario_compartilhado');
        $caminho = $pergunta === null ? $form->imagem : $form->perguntas()->findOrFail($pergunta)->imagem;
        abort_unless($caminho && Storage::disk('local')->exists($caminho), 404);

        return Storage::disk('local')->response($caminho, null, ['X-Content-Type-Options' => 'nosniff']);
    }
}
