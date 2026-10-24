@extends('layouts.app')
@section('title', 'Formulário já respondido | Formora')
@push('styles')
@vite('resources/css/paginas/formularios/compartilhamento.css')
@endpush
@section('content')
<section class="closed-form">
    <span class="eyebrow">RESPOSTA REGISTRADA</span>
    <h1>Você já respondeu a este formulário.</h1>
    <p><strong>{{ $form->titulo }}</strong> permite somente uma resposta por usuário. Não é possível fazer um novo envio com seu login.</p>
    <small>Se precisar de ajuda, procure o responsável pelo formulário.</small>
</section>
@endsection