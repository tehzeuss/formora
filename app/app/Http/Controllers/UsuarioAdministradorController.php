<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalvarUsuarioRequest;
use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioAdministradorController extends Controller
{
    public function listar(Request $request)
    {
        $dados = $request->validate(['busca' => ['nullable', 'string', 'max:100']]);
        $busca = trim($dados['busca'] ?? '');
        $usuarios = User::with('perfil')->when($busca !== '', fn ($query) => $query->where(fn ($query) => $query
            ->whereLike('name', '%'.$busca.'%')->orWhereLike('email', '%'.$busca.'%')))
            ->orderBy('name')->paginate(15)->withQueryString();

        $resumo = User::resumoAdministrativo();

        return view('administracao.usuarios.listar', compact('usuarios', 'busca', 'resumo'));
    }

    public function criar()
    {
        return view('administracao.usuarios.editar', ['usuario' => new User, 'perfis' => Perfil::orderBy('id')->get()]);
    }

    public function salvar(SalvarUsuarioRequest $request)
    {
        User::cadastrarConta($request->validated());

        return redirect()->route('administracao.usuarios.listar')->with('status', 'Conta cadastrada. O usuário já pode entrar com e-mail e senha.');
    }

    public function editar(User $usuario)
    {
        return view('administracao.usuarios.editar', ['usuario' => $usuario, 'perfis' => Perfil::orderBy('id')->get()]);
    }

    public function atualizar(SalvarUsuarioRequest $request, User $usuario)
    {
        $usuario->atualizarCadastroAdministrativo($request->validated());

        return redirect()->route('administracao.usuarios.listar')->with('status', 'Cadastro atualizado.');
    }
}
