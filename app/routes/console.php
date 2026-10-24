<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('formora:admin', function () {
    $dados = [
        'name' => trim($this->ask('Nome do administrador')),
        'email' => mb_strtolower(trim($this->ask('E-mail'))),
        'password' => $this->secret('Senha (mínimo de 8 caracteres, letras e números)'),
        'password_confirmation' => $this->secret('Confirme a senha'),
    ];
    $validado = \Illuminate\Support\Facades\Validator::make($dados, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:254', 'unique:users,email'],
        'password' => ['required', 'string', 'max:72', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()],
    ])->validate();
    \App\Models\User::cadastrarConta([...$validado, 'perfil_id' => \App\Models\Perfil::ADMINISTRADOR, 'ativo' => true]);
    $this->info('Administrador criado. Entre com o e-mail e a senha escolhidos.');
})->purpose('Criar um administrador local sem senha padrão');
