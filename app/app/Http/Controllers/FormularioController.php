<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalvarFormularioRequest;
use App\Models\Formulario;
use App\Models\PerguntaFormulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index(Request $request)
    {
        $forms = Formulario::where('usuario_id', $request->user()->id)->withCount(['perguntas', 'envios'])->latest('atualizado_em')->paginate(12);
        $resumo = Formulario::resumoDoUsuario($request->user()->id);

        return view('formularios.listar', compact('forms', 'resumo'));
    }

    public function create()
    {
        return view('formularios.criar', ['form' => new Formulario, 'types' => PerguntaFormulario::TYPES]);
    }

    public function store(SalvarFormularioRequest $request)
    {
        $form = new Formulario;
        $form->usuario_id = $request->user()->id;
        $form->salvarConteudo($request->validated());

        return redirect()->route('forms.edit', $form)->with('status', 'Formulário criado.');
    }

    public function edit(Request $request, Formulario $form)
    {
        $this->authorizeOwner($request, $form);
        $form->load('perguntas');

        return view('formularios.editar', ['form' => $form, 'types' => PerguntaFormulario::TYPES]);
    }

    public function update(SalvarFormularioRequest $request, Formulario $form)
    {
        $this->authorizeOwner($request, $form);
        $form->salvarConteudo($request->validated());

        return redirect()->route('forms.edit', $form)->with('status', 'Alterações salvas.');
    }

    public function show(Request $request, Formulario $form)
    {
        $this->authorizeOwner($request, $form);
        $form->load('perguntas');

        return view('formularios.visualizar', compact('form'));
    }

    private function authorizeOwner(Request $request, Formulario $form): void
    {
        abort_unless($form->usuario_id === $request->user()->id, 404);
    }

    public function imagem(Request $request, Formulario $form, ?PerguntaFormulario $pergunta = null)
    {
        $this->authorizeOwner($request, $form);
        if ($pergunta) {
            abort_unless($pergunta->formulario_id === $form->id, 404);
        }
        $caminho = $pergunta ? $pergunta->imagem : $form->imagem;
        abort_unless($caminho && \Illuminate\Support\Facades\Storage::disk('local')->exists($caminho), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->response($caminho, null, [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

}
