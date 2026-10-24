@extends('layouts.app')
@section('title', 'Cadastro de usuário | Formora')
@push('styles')
    @vite('resources/css/paginas/administracao.css')
@endpush
@section('content')
<section class="admin-page">
    @include('administracao.parciais.navegacao')
    <div class="admin-heading"><div><span class="admin-eyebrow">ADMINISTRAÇÃO</span><h1>{{ $usuario->exists ? 'Editar usuário' : 'Cadastrar usuário' }}</h1><p>O acesso exige senha válida no e-mail, senha e conta ativa.</p></div></div>
    <form class="admin-panel admin-form" method="post" action="{{ $usuario->exists ? route('administracao.usuarios.atualizar', $usuario) : route('administracao.usuarios.salvar') }}">
        @csrf
        @if($usuario->exists)
            @method('put')
        @endif
        <label for="nome">Nome<input id="nome" name="name" value="{{ old('name', $usuario->name) }}" maxlength="255" required autocomplete="name"></label>
        <label for="email">E-mail<input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" maxlength="254" autocomplete="email" required></label>
        <label for="password">Senha {{ $usuario->exists ? '(deixe em branco para manter)' : '' }}<input id="password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" @required(!$usuario->exists)></label>
        <label for="password_confirmation">Confirmar senha<input id="password_confirmation" type="password" name="password_confirmation" minlength="8" maxlength="72" autocomplete="new-password"></label>
        <small>Use pelo menos 8 caracteres, com letras e números.</small>
        <label for="perfil">Perfil<select id="perfil" name="perfil_id" required>
            @foreach($perfis as $perfil)
                <option value="{{ $perfil->id }}" @selected((int) old('perfil_id', $usuario->perfil_id ?? 1) === $perfil->id)>{{ $perfil->nome }}</option>
            @endforeach
        </select></label>
        <label for="ativo">Situação<select id="ativo" name="ativo" required><option value="1" @selected((string) old('ativo', $usuario->exists ? (int) $usuario->ativo : 1) === '1')>Ativo</option><option value="0" @selected((string) old('ativo', $usuario->exists ? (int) $usuario->ativo : 1) === '0')>Inativo</option></select></label>
        <div class="admin-actions"><button class="button" type="submit">{{ $usuario->exists ? 'Salvar alterações' : 'Cadastrar usuário' }}</button><a href="{{ route('administracao.usuarios.listar') }}">Cancelar</a></div>
    </form>
</section>
@endsection
