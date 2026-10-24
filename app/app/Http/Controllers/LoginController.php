<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function autenticar(LoginRequest $request)
    {
        $email = $request->validated('email');
        $chave = 'login:'.hash('sha256', $email.'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($chave, 5)) {
            throw ValidationException::withMessages(['email' => 'Muitas tentativas. Aguarde '.RateLimiter::availableIn($chave).' segundos.']);
        }
        RateLimiter::hit($chave, 60);
        if (! Auth::attempt(['email' => $email, 'password' => $request->validated('password'), 'ativo' => true])) {
            throw ValidationException::withMessages(['email' => 'E-mail ou senha inválidos, ou conta inativa.']);
        }
        $request->session()->regenerate();
        RateLimiter::clear($chave);

        return redirect()->intended('/');
    }

    public function deslogar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
