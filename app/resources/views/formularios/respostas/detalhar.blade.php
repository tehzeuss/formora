@extends('layouts.app')
@section('title', 'Envio #'.$envio->id.' | Respostas')
@push('styles')
    @vite('resources/css/paginas/formularios/respostas.css')
@endpush
@section('content')
<section class="responses-page response-detail">
    <a class="responses-back" href="{{ route('formularios.respostas.listar', ['form' => $form, 'aba' => 'individual']) }}"><x-icone name="arrow-left" /> Voltar às respostas</a>
    @include('formularios.respostas.parciais.envio')
</section>
@endsection
