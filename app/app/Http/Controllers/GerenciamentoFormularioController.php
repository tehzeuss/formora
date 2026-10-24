<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExcluirFormularioRequest;
use App\Http\Requests\GerenciarFormularioRequest;
use App\Models\Formulario;

class GerenciamentoFormularioController extends Controller
{
    public function duplicar(GerenciarFormularioRequest $request, Formulario $form)
    {
        $copia = $form->duplicarParaProprietario($request->user()->id);

        return redirect()->route('forms.edit', $copia)->with('status', 'Formulário duplicado como rascunho. As respostas e o link não foram copiados.');
    }

    public function confirmarExclusao(GerenciarFormularioRequest $request, Formulario $form)
    {
        $form->loadCount(['perguntas', 'envios']);

        return view('formularios.excluir', compact('form'));
    }

    public function excluir(ExcluirFormularioRequest $request, Formulario $form)
    {
        $form->excluirComConteudo($request->user()->id);

        return redirect()->route('forms.index')->with('status', 'Formulário e suas respostas excluídos.');
    }
}
