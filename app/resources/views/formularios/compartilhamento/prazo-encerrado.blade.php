@extends('layouts.app')
@section('title', 'Prazo encerrado | Formora')
@push('styles')
    @vite('resources/css/paginas/formularios/compartilhamento.css')
@endpush
@section('content')
<section class="closed-form">
    <span class="eyebrow">PRAZO DE RESPOSTA ENCERRADO</span>
    <h1>{{ $form->titulo }}</h1>
    <p>O prazo para responder a este formulário terminou em <strong>{{ $form->prazo_encerramento->timezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}</strong>, no horário de Brasília. Novas respostas não são mais aceitas.</p>
    <small>Se precisar de informações, entre em contato com o responsável pelo formulário.</small>
</section>
@endsection
