<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferirFormularioRequest;
use App\Models\Formulario;
use App\Models\PerguntaFormulario;
use App\Models\TransferenciaFormulario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormularioAdministradorController extends Controller
{
    public function listar(Request $request)
    {
        $dados = $request->validate(['busca' => ['nullable', 'string', 'max:100']]);
        $busca = trim($dados['busca'] ?? '');
        $formularios = Formulario::with('usuario')->withCount('perguntas')
            ->when($busca !== '', fn ($query) => $query->where(fn ($query) => $query->whereLike('titulo', '%'.$busca.'%')
                ->orWhereHas('usuario', fn ($query) => $query->whereLike('name', '%'.$busca.'%'))))
            ->latest('atualizado_em')->paginate(15)->withQueryString();

        $resumo = Formulario::resumoAdministrativo();

        return view('administracao.formularios.listar', compact('formularios', 'busca', 'resumo'));
    }

    public function visualizar(Formulario $form)
    {
        $form->load(['usuario', 'perguntas']);

        return view('administracao.formularios.visualizar', compact('form'));
    }

    public function transferencia(Formulario $form)
    {
        $form->load('usuario');
        $usuarios = User::where('ativo', true)->whereKeyNot($form->usuario_id)->orderBy('name')->get(['id', 'name', 'email']);
        $historico = TransferenciaFormulario::where('formulario_id', $form->id)
            ->with(['administrador', 'proprietarioAnterior', 'proprietarioNovo'])->latest('id')->paginate(10);

        return view('administracao.formularios.transferir', compact('form', 'usuarios', 'historico'));
    }

    public function transferir(TransferirFormularioRequest $request, Formulario $form)
    {
        $form->transferirPropriedade($request->user(), (int) $request->validated('usuario_id'), (int) $request->validated('proprietario_anterior_id'));

        return redirect()->route('administracao.formularios.transferencia', $form)->with('status', 'Propriedade transferida.');
    }

    public function imagem(Formulario $form, ?PerguntaFormulario $pergunta = null)
    {
        abort_if($pergunta && $pergunta->formulario_id !== $form->id, 404);
        $caminho = $pergunta ? $pergunta->imagem : $form->imagem;
        abort_unless($caminho && Storage::disk('local')->exists($caminho), 404);

        return Storage::disk('local')->response($caminho, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
