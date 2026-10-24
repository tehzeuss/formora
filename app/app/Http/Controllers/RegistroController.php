<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUsuarioRequest;
use App\Models\Perfil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegistroController extends Controller
{
    public function criar()
    {
        return view('auth.registro');
    }

    public function salvar(RegistrarUsuarioRequest $request)
    {
        $usuario = User::cadastrarConta([...$request->validated(), 'perfil_id' => Perfil::USUARIO, 'ativo' => true]);
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
