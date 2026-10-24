<div class="admin-overview">
    <div class="admin-overview-content">
        <span class="admin-overview-kicker">ÁREA ADMINISTRATIVA</span>
        <h1>{{ $titulo }}</h1>
        <p>{{ $descricao }}</p>
        @if($icone === 'usuarios')
            <a class="admin-overview-action" href="{{ route('administracao.usuarios.criar') }}"><x-icone name="plus" /> Cadastrar usuário</a>
        @else
            <a class="admin-overview-action" href="{{ route('administracao.usuarios.listar') }}">Gerenciar usuários <x-icone name="arrow-right" /></a>
        @endif
    </div>
    <div class="admin-overview-art" aria-hidden="true">
        <svg width="180" height="150" viewBox="0 0 180 150" fill="none">
            <rect x="17" y="15" width="145" height="120" rx="14" fill="white" fill-opacity=".08"/>
            @if($icone === 'usuarios')
                <circle cx="90" cy="49" r="17" stroke="#dfecf5" stroke-width="3"/>
                <path d="M57 112v-10a33 33 0 0 1 66 0v10" stroke="#dfecf5" stroke-width="3" stroke-linecap="round"/>
                <path d="M49 42a13 13 0 0 0 0 26M131 42a13 13 0 0 1 0 26M33 104v-6a25 25 0 0 1 14-23M147 104v-6a25 25 0 0 0-14-23" stroke="#93b7cc" stroke-width="3" stroke-linecap="round"/>
            @else
                <rect x="46" y="28" width="87" height="96" rx="7" fill="#eef5fa"/>
                <path d="M62 48h36M62 63h54M76 83h40M76 101h40" stroke="#6d91aa" stroke-width="4" stroke-linecap="round"/>
                <circle cx="64" cy="83" r="4" stroke="#6d91aa" stroke-width="2"/><circle cx="64" cy="101" r="4" stroke="#6d91aa" stroke-width="2"/>
            @endif
            <circle cx="148" cy="119" r="20" fill="#81b7b1"/>
            <path d="m139 119 6 6 12-13" stroke="#163e4c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>
<dl class="admin-stats" aria-label="Resumo geral, independente da busca">
    @foreach($indicadores as $indicador)
        <div><dt>{{ $indicador['rótulo'] }}</dt><dd>{{ $indicador['valor'] }}</dd><span>{{ $indicador['detalhe'] }}</span></div>
    @endforeach
</dl>
