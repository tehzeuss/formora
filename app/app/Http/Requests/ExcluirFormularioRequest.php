<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ExcluirFormularioRequest extends GerenciarFormularioRequest
{
    public function rules(): array
    {
        return [
            'confirmacao' => ['required', Rule::in(['EXCLUIR'])],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmacao.required' => 'Digite EXCLUIR para confirmar a exclusão.',
            'confirmacao.in' => 'Digite EXCLUIR para confirmar a exclusão.',
        ];
    }
}
