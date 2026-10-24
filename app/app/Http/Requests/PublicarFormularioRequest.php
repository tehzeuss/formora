<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicarFormularioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->ativo === true
            && $this->route('form')?->usuario_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [];
    }
}
