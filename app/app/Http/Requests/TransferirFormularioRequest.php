<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferirFormularioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->ativo && $this->user()->eAdministrador();
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required', 'integer', Rule::exists('users', 'id')->where('ativo', true)],
            'proprietario_anterior_id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return ['usuario_id.required' => 'Selecione o novo proprietário.', 'usuario_id.exists' => 'Selecione um usuário local ativo.'];
    }
}
