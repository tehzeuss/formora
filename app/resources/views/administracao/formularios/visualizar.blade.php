@extends('layouts.app')
@section('title', 'Visualizar formulário | Administração')
@push('styles')
    @vite(['resources/css/paginas/administracao.css', 'resources/css/paginas/formularios/visualizar.css'])
@endpush
@push('scripts')
    @vite('resources/js/paginas/formularios/visualizar.js')
@endpush
@section('content')
<div class="admin-page">
    @include('administracao.parciais.navegacao')
    <div class="admin-heading"><div><h1>Visualizar formulário</h1><p>Proprietário: {{ $form->usuario->name }}</p></div><a class="button" href="{{ route('administracao.formularios.transferencia', $form) }}">Transferir propriedade</a></div>
    <p class="admin-notice">Visualização das perguntas. Nenhuma resposta é exibida ou enviada nesta página.</p>
    <div class="form-preview">
        @include('formularios.parciais.conteudo', ['administrativo' => true])
    </div>
</div>
@endsection
