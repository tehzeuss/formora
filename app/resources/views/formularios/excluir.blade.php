@extends('layouts.app')
@section('title', 'Excluir formulário | Formora')
@push('styles')
    @vite('resources/css/paginas/formularios/excluir.css')
@endpush
@section('content')
<section class="delete-page">
    <a class="delete-back" href="{{ route('forms.edit', $form) }}"><x-icone name="arrow-left" /> Voltar ao formulário</a>
    <div class="delete-panel">
        <span class="delete-symbol"><x-icone name="trash" :size="28" /></span>
        <h1>Excluir formulário?</h1>
        <p class="delete-title">{{ $form->titulo }}</p>
        <p>Esta ação é permanente. Serão removidas as <strong>{{ $form->perguntas_count }} perguntas</strong>, as imagens e os <strong>{{ $form->envios_count }} envios de respostas</strong> deste formulário. O link de compartilhamento deixará de funcionar.</p>
        <a class="delete-export" href="{{ route('formularios.respostas.exportar', $form) }}">Exportar respostas em CSV antes de excluir <x-icone name="arrow-right" /></a>
        @if($errors->any())
            <div class="delete-error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('formularios.excluir', $form) }}">
            @csrf
            @method('delete')
            <label for="confirmacao">Digite EXCLUIR para confirmar</label>
            <input id="confirmacao" name="confirmacao" autocomplete="off" spellcheck="false" required pattern="EXCLUIR" aria-describedby="delete-help">
            <small id="delete-help">A exclusão não pode ser desfeita.</small>
            <div class="delete-actions"><button type="submit">Excluir permanentemente</button><a href="{{ route('forms.index') }}">Cancelar</a></div>
        </form>
    </div>
</section>
@endsection
