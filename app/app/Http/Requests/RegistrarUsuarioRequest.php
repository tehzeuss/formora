<?php

namespace App\Http\Requests;

class RegistrarUsuarioRequest extends SalvarUsuarioRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    public function rules(): array
    {
        return array_intersect_key(parent::rules(), array_flip(['name', 'email', 'password']));
    }
}
