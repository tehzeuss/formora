<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicarFormularioRequest;
use App\Models\Formulario;

class PublicacaoFormularioController extends Controller
{
    public function publicar(PublicarFormularioRequest $request, Formulario $form)
    {
        $form->publicar();

        return redirect()->route('forms.edit', $form)->with('status', 'Formulário publicado. O link já pode ser compartilhado.');
    }

    public function encerrar(PublicarFormularioRequest $request, Formulario $form)
    {
        $form->encerrar();

        return redirect()->route('forms.edit', $form)->with('status', 'Formulário encerrado. O link informa que o recebimento foi encerrado.');
    }
}
