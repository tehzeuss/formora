@extends('layouts.app')
@section('title', 'Gerenciar usuários | Formora')
@push('styles')
    @vite('resources/css/paginas/administracao.css')
@endpush
@section('content')
<section class="admin-page">
    @include('administracao.parciais.navegacao')
    @include('administracao.parciais.visao-geral', [
        'titulo' => 'Pessoas e acessos',
        'descricao' => 'Organize quem faz parte do sistema. Cadastre usuários, defina perfis e mantenha os acessos atualizados.',
        'icone' => 'usuarios',
        'indicadores' => [
            ['rótulo' => 'Usuários cadastrados', 'valor' => $resumo['total'], 'detalhe' => 'Cadastros no sistema'],
            ['rótulo' => 'Ativos', 'valor' => $resumo['ativos'], 'detalhe' => 'Com acesso habilitado'],
            ['rótulo' => 'Inativos', 'valor' => $resumo['inativos'], 'detalhe' => 'Com acesso desabilitado'],
            ['rótulo' => 'Administradores', 'valor' => $resumo['administradores'], 'detalhe' => 'Perfis administrativos'],
        ],
    ])
    <div class="admin-heading"><div><h2>Usuários cadastrados</h2><p>O acesso exige credenciais válidas no e-mail, senha e conta ativa.</p></div><a class="button" href="{{ route('administracao.usuarios.criar') }}"><x-icone name="plus" /> Cadastrar usuário</a></div>
    <form class="admin-search" method="get">
        <label for="busca">Buscar por nome ou e-mail</label>
        <div><input id="busca" name="busca" value="{{ $busca }}" maxlength="100"><button class="button" type="submit">Buscar</button></div>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr><td>{{ $usuario->name }}</td><td>{{ $usuario->email }}</td><td>{{ $usuario->perfil?->nome }}</td><td><span class="admin-badge {{ $usuario->ativo ? '' : 'is-inactive' }}">{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</span></td><td><a href="{{ route('administracao.usuarios.editar', $usuario) }}">Editar cadastro</a></td></tr>
                @empty
                    <tr><td colspan="5">Nenhum usuário encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $usuarios->links() }}
</section>
@endsection
