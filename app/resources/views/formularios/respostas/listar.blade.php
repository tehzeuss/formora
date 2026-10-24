@extends('layouts.app')
@section('title', 'Respostas | '.$form->titulo)
@push('styles')
    @vite('resources/css/paginas/formularios/respostas.css')
@endpush
@section('content')
<section class="responses-page response-overview">
    <a class="responses-back" href="{{ route('forms.edit', $form) }}"><x-icone name="arrow-left" /> Voltar ao formulário</a>
    <a class="responses-export" href="{{ route('formularios.respostas.exportar', $form) }}"><x-icone name="download" /> Exportar CSV</a>
    <header class="response-overview-heading">
        <div class="overview-title"><div><h1>{{ $total }} {{ $total === 1 ? 'resposta' : 'respostas' }}</h1><p>{{ $form->titulo }}</p></div><span class="overview-status">{{ $form->rotuloSituacao() }}</span></div>
        <nav class="response-tabs" aria-label="Visualização das respostas">
            @foreach(['resumo' => 'Resumo', 'pergunta' => 'Pergunta', 'individual' => 'Individual'] as $valor => $rotulo)
                <a href="{{ route('formularios.respostas.listar', ['form' => $form, 'aba' => $valor]) }}" class="{{ $aba === $valor ? 'active' : '' }}" @if($aba === $valor) aria-current="page" @endif>{{ $rotulo }}</a>
            @endforeach
        </nav>
    </header>
    @if($total === 0)
        <div class="responses-empty"><h2>Aguardando respostas.</h2><p>Compartilhe o formulário. Os resultados aparecerão aqui conforme os envios forem recebidos.</p><a href="{{ route('forms.edit', $form) }}">Publicação e compartilhamento <x-icone name="arrow-right" /></a></div>
    @elseif($aba === 'individual')
        @include('formularios.respostas.parciais.individuais')
    @else
        @if($aba === 'pergunta')
            <form class="question-selector" method="get" action="{{ route('formularios.respostas.listar', $form) }}">
                <input type="hidden" name="aba" value="pergunta">
                <label for="summary-question">Selecionar pergunta</label>
                <div><select id="summary-question" name="pergunta">@foreach($resumo as $chave => $item)<option value="{{ $chave }}" @selected($chave === $perguntaSelecionada)>{{ $loop->iteration }}. {{ $item['titulo'] }}{{ !$item['atual'] ? ' (versão anterior)' : '' }}</option>@endforeach</select><button class="button" type="submit">Ver pergunta</button></div>
            </form>
        @endif
        @foreach($resumo as $chave => $grupo)
            @if($aba === 'resumo' || $chave === $perguntaSelecionada)
                @include('formularios.respostas.parciais.resumo-pergunta')
            @endif
        @endforeach
    @endif
</section>
@endsection
