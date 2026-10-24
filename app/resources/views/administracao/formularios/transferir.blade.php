@extends('layouts.app')
@section('title', 'Transferir formulário | Formora')
@push('styles')
    @vite('resources/css/paginas/administracao.css')
@endpush
@section('content')
<section class="admin-page">
    @include('administracao.parciais.navegacao')
    <div class="admin-heading"><div><span class="admin-eyebrow">ADMINISTRAÇÃO</span><h1>Transferir propriedade</h1><p>{{ $form->titulo }}</p></div></div>
    <form class="admin-panel admin-form" method="post" action="{{ route('administracao.formularios.transferir', $form) }}">
        @csrf
        <input type="hidden" name="proprietario_anterior_id" value="{{ $form->usuario_id }}">
        <p>Proprietário atual: <strong>{{ $form->usuario->name }}</strong></p>
        <p>O novo proprietário poderá gerenciar o formulário e consultar suas respostas. O proprietário anterior perderá esse acesso. O link, a publicação e as respostas serão preservados.</p>
        <label for="usuario">Novo proprietário<select id="usuario" name="usuario_id" required><option value="">Selecione um usuário ativo</option>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected((string) old('usuario_id') === (string) $usuario->id)>{{ $usuario->name }} — {{ $usuario->email }}</option>
            @endforeach
        </select></label>
        <div class="admin-actions"><button class="button" type="submit" @disabled($usuarios->isEmpty())>Confirmar transferência</button><a href="{{ route('administracao.formularios.listar') }}">Voltar</a></div>
        @if($usuarios->isEmpty())
            <p>Nenhum outro usuário ativo disponível.</p>
        @endif
    </form>
    <h2>Histórico de transferências</h2>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Data</th><th>Administrador</th><th>Proprietário anterior</th><th>Novo proprietário</th></tr></thead><tbody>
        @forelse($historico as $transferencia)
            <tr><td>{{ $transferencia->transferido_em->format('d/m/Y H:i') }}</td><td>{{ $transferencia->administrador->name }}</td><td>{{ $transferencia->proprietarioAnterior->name }}</td><td>{{ $transferencia->proprietarioNovo->name }}</td></tr>
        @empty
            <tr><td colspan="4">Nenhuma transferência realizada.</td></tr>
        @endforelse
    </tbody></table></div>
    {{ $historico->links() }}
</section>
@endsection
