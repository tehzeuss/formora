@extends('layouts.app')
@section('title', 'Criar formulário | Formora')
@push('styles') @vite('resources/css/paginas/formularios/editor.css') @endpush
@push('scripts') @vite('resources/js/paginas/formularios/editor.js') @endpush
@section('content')
@include('formularios.parciais.editor')
@endsection