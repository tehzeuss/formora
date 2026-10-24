@extends('layouts.app')
@section('title', 'Resposta enviada | Formora')
@push('styles')
    @vite('resources/css/paginas/formularios/compartilhamento.css')
@endpush
@section('content')
<section class="closed-form submission-confirmation">
    <span class="confirmation-icon" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4L19 6"/></svg>
    </span>
    <h1>Resposta enviada.</h1>
    <p>Sua resposta ao formulário <strong>{{ $form->titulo }}</strong> foi registrada com sucesso.</p>
    @if($form->modo_acesso === 'aberto_livre')
        <a class="submission-again" href="{{ route('compartilhamento.exibir', $form->token_compartilhamento) }}">Enviar outra resposta <x-icone name="arrow-right" /></a>
    @else
        <small>É permitido um envio por {{ $form->exigeLogin() ? 'usuário' : ($form->tipo_identificador === 'cpf' ? 'CPF' : 'e-mail') }}. Você já pode fechar esta página.</small>
    @endif
</section>
@endsection
