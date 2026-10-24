@extends('layouts.app')
@section('title', 'Formulário encerrado | Formora')
@push('styles')
    @vite('resources/css/paginas/formularios/compartilhamento.css')
@endpush
@section('content')
<section class="closed-form">
    <span class="eyebrow">RECEBIMENTO ENCERRADO</span>
    <h1>{{ $form->titulo }}</h1>
    <p>Este formulário não está aceitando respostas.</p>
    <small>Se precisar de informações, entre em contato com o responsável pelo formulário.</small>
</section>
@endsection
