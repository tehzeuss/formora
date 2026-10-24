<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnviarRespostasRequest;
use App\Http\Requests\ConsultarRespostasRequest;
use App\Models\EnvioFormulario;
use App\Models\Formulario;

class RespostaFormularioController extends Controller
{
    public function listar(ConsultarRespostasRequest $request, Formulario $form)
    {
        $aba = $request->validated('aba') ?? 'resumo';
        $total = $form->envios()->count();
        $resumo = $aba !== 'individual' ? $form->resumoRespostas() : [];
        $perguntaSelecionada = $request->validated('pergunta') ?? array_key_first($resumo);
        if ($aba === 'pergunta' && $perguntaSelecionada !== null) {
            abort_unless(isset($resumo[$perguntaSelecionada]), 404);
        }
        $envios = $aba === 'individual'
            ? $form->envios()->with(['usuario:id,name', 'respostas'])->orderBy('id')->paginate(1)->withQueryString()
            : null;

        return view('formularios.respostas.listar', compact('form', 'envios', 'aba', 'total', 'resumo', 'perguntaSelecionada'));
    }

    public function detalhar(ConsultarRespostasRequest $request, Formulario $form, EnvioFormulario $envio)
    {
        abort_unless($envio->formulario_id === $form->id, 404);
        $envio->load(['usuario:id,name', 'respostas']);

        return view('formularios.respostas.detalhar', compact('form', 'envio'));
    }

    public function enviar(EnviarRespostasRequest $request)
    {
        $formulario = $request->formulario();
        EnvioFormulario::registrar($formulario, $request->validated(), $request->user());

        return redirect()->route('compartilhamento.exibir', $formulario->token_compartilhamento)
            ->with('envio_confirmado', $formulario->id);
    }
}
