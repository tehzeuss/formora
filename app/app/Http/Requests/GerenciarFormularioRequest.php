<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GerenciarFormularioRequest extends FormRequest
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
        return [];
    }
}
