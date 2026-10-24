@extends('layouts.app')
@section('title', 'Entrar | Formora')
@section('content')
<section class="login" aria-labelledby="login-title">
    <aside class="login-presentation" aria-labelledby="presentation-title">
        <span class="product-tag">FORMORA</span>
        <h1 id="presentation-title">Formulários<br><span>do seu jeito.</span></h1>
        <p>Informações organizadas.<br>Uma gestão mais simples.</p>
        <div class="document-preview" aria-hidden="true">
            <div class="preview-top"><span class="preview-mark"></span><span>FORMORA</span></div>
            <div class="preview-title"></div>
            <div class="preview-caption"></div>
            <div class="preview-label"></div>
            <div class="preview-input"></div>
            <div class="preview-label short"></div>
            <div class="preview-options"><i></i><span></span><i></i><span></span></div>
            <div class="preview-bottom"><span></span><b>✓</b></div>
        </div>
        <span class="presentation-caption">Da primeira pergunta à próxima grande ideia.</span>
    </aside>
    <div class="login-content">
        <span class="eyebrow"><span class="access-dot" aria-hidden="true"></span> ÁREA RESTRITA</span>
        <h2 id="login-title">Entrar</h2>
        <p class="login-description">Entre com seu e-mail e senha.</p>
        @if(session('aviso'))
            <p class="login-description" role="status">{{ session('aviso') }}</p>
        @endif
        @if($errors->any())
        <div class="alert" role="alert">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif
        <form action="{{ route('login.autenticar') }}" method="post">
            @csrf
            <label for="email">E-mail <span aria-hidden="true">*</span></label>
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" maxlength="254" placeholder="voce@exemplo.com" required autofocus @error('email') aria-invalid="true" @enderror>
            <label for="password">Senha <span aria-hidden="true">*</span></label>
            <div class="password-field">
                <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Digite sua senha" required @error('password') aria-invalid="true" @enderror>
                <button class="password-toggle" type="button" aria-label="Mostrar senha" aria-controls="password" aria-pressed="false" data-password-toggle>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <button class="button submit" type="submit">Entrar no sistema <span aria-hidden="true">→</span></button>
        </form>
        <div class="login-note"><svg width="17" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <rect x="5" y="10" width="14" height="11" rx="2" />
                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
            </svg><span>Acesso restrito a usuários cadastrados e ativos.</span></div>
        <p class="help">Ainda não tem conta? <a href="{{ route('registro.criar') }}">Criar conta</a></p>
    </div>
</section>
@endsection
