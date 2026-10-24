@extends('layouts.app')
@section('title', 'Visualizar formulário | Formora')
@push('styles') @vite('resources/css/paginas/formularios/visualizar.css') @endpush
@push('scripts') @vite('resources/js/paginas/formularios/visualizar.js') @endpush
@section('content')
<div class="form-preview">
    <div class="preview-toolbar"><a href="{{ route('forms.edit', $form) }}"><x-icone name="arrow-left" /> Voltar à edição</a><span>Pré-visualização · Respostas não são enviadas</span></div>
    @include('formularios.parciais.conteudo')
    <p class="preview-disclaimer">Esta é uma pré-visualização do formulário. Nenhuma resposta será armazenada.</p>
</div>
@endsection
