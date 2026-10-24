@extends('layouts.app')
@section('title', 'Todos os formulários | Formora')
@push('styles')
    @vite('resources/css/paginas/administracao.css')
@endpush
@section('content')
<section class="admin-page">
    @include('administracao.parciais.navegacao')
    @include('administracao.parciais.visao-geral', [
        'titulo' => 'Uma visão de todos os formulários',
        'descricao' => 'Acompanhe os formulários da plataforma, consulte suas perguntas e gerencie quem é responsável por cada um.',
        'icone' => 'formularios',
        'indicadores' => [
            ['rótulo' => 'Formulários', 'valor' => $resumo['total'], 'detalhe' => 'De todos os proprietários'],
            ['rótulo' => 'Publicados', 'valor' => $resumo['publicados'], 'detalhe' => 'Disponíveis para responder'],
            ['rótulo' => 'Rascunhos', 'valor' => $resumo['rascunhos'], 'detalhe' => 'Em preparação'],
            ['rótulo' => 'Encerrados', 'valor' => $resumo['encerrados'], 'detalhe' => 'Recebimento finalizado'],
        ],
    ])
    <div class="admin-heading"><div><h2>Todos os formulários</h2><p>Visualização das perguntas e transferência de propriedade. As respostas permanecem restritas ao proprietário.</p></div></div>
    <form class="admin-search" method="get"><label for="busca">Buscar por título ou proprietário</label><div><input id="busca" name="busca" value="{{ $busca }}" maxlength="100"><button class="button" type="submit">Buscar</button></div></form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Formulário</th><th>Proprietário</th><th>Situação</th><th>Perguntas</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($formularios as $form)
                    <tr><td>{{ $form->titulo }}</td><td>{{ $form->usuario->name }}</td><td>{{ $form->rotuloSituacao() }}</td><td>{{ $form->perguntas_count }}</td><td><div class="admin-actions"><a href="{{ route('administracao.formularios.visualizar', $form) }}">Visualizar</a><a href="{{ route('administracao.formularios.transferencia', $form) }}">Transferir</a></div></td></tr>
                @empty
                    <tr><td colspan="5">Nenhum formulário encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $formularios->links() }}
</section>
@endsection
