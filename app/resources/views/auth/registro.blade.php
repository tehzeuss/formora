@extends('layouts.app')
@section('title', 'Criar conta | Formora')
@section('content')
<section class="login" aria-labelledby="registro-title">
    <aside class="login-presentation">
        <span class="product-tag">FORMORA</span>
        <h1>Boas perguntas.<br><span>Novas ideias.</span></h1>
        <p>Crie formulários do seu jeito e acompanhe as respostas em um só lugar.</p>
    </aside>
    <div class="login-content">
        <span class="eyebrow">SEU ESPAÇO DE CRIAÇÃO</span>
        <h2 id="registro-title">Criar conta</h2>
        <p class="login-description">Comece a criar e compartilhar seus formulários.</p>
        @if($errors->any())
            <div class="alert" role="alert"><ul>@foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul></div>
        @endif
        <form method="post" action="{{ route('registro.salvar') }}">
            @csrf
            <label for="name">Nome</label>
            <input id="name" name="name" value="{{ old('name') }}" maxlength="255" autocomplete="name" required autofocus>
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="254" autocomplete="email" required>
            <label for="password">Senha</label>
            <input id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
            <label for="password_confirmation">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
            <p class="login-description">Use pelo menos 8 caracteres, com letras e números.</p>
            <button class="button submit" type="submit">Criar minha conta</button>
        </form>
        <p class="help">Já tem uma conta? <a href="{{ route('login') }}">Entrar</a></p>
    </div>
</section>
@endsection
