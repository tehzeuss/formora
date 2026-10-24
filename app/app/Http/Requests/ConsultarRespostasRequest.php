<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultarRespostasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->ativo === true
            && $this->route('form')?->usuario_id === $this->user()->id;
    }

    protected function failedAuthorization(): void
    {
        abort(404);
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
            'aba' => ['sometimes', \Illuminate\Validation\Rule::in(['resumo', 'pergunta', 'individual'])],
            'pergunta' => ['sometimes', 'string', 'regex:/^[a-f0-9]{64}$/'],
        ];
    }
}
