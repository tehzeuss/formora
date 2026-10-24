<?php

namespace App\Http\Requests;

use App\Models\Perfil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SalvarUsuarioRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->ativo && $this->user()->eAdministrador();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users', 'email')->ignore($this->route('usuario'))],
            'password' => [$this->route('usuario') ? 'nullable' : 'required', 'string', 'max:72', 'confirmed', Password::min(8)->letters()->numbers()],
            'perfil_id' => ['required', 'integer', Rule::in([Perfil::USUARIO, Perfil::ADMINISTRADOR])],
            'ativo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome.', 'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.', 'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'Informe a senha inicial.', 'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.letters' => 'A senha deve conter letras.', 'password.numbers' => 'A senha deve conter números.',
            'perfil_id.in' => 'Selecione um perfil válido.', 'ativo.boolean' => 'Selecione uma situação válida.',
        ];
    }
}
