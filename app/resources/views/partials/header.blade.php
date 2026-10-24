<header class="header">
    <div class="header-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Formora — início">
            <img class="brand-logo" src="{{ asset('images/formora.svg') }}" alt="" width="52" height="52">
            <span class="brand-name">Formora<span>CRIE. COMPARTILHE. DESCUBRA.</span></span>
        </a>
        @auth
            <details class="user-menu" data-user-menu>
                <summary class="user-menu-trigger" aria-label="Opções da conta de {{ auth()->user()->name }}">
                    <span class="user-avatar" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>
                    </span>
                    <span class="user-identity">
                        <span class="user-first-name">{{ \Illuminate\Support\Str::before(trim(auth()->user()->name), ' ') }}</span>
                        <span class="user-role">{{ auth()->user()->ativo && auth()->user()->eAdministrador() ? 'Administrador' : 'Usuário' }}</span>
                    </span>
                    <svg class="user-menu-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <div class="user-menu-panel">
                    @if(auth()->user()->ativo && auth()->user()->eAdministrador())
                        <a class="user-logout" href="{{ route('administracao.usuarios.listar') }}">Gerenciar usuários</a>
                        <a class="user-logout" href="{{ route('administracao.formularios.listar') }}">Todos os formulários</a>
                    @endif
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="user-logout" type="submit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M10 17v3H4V4h6v3m4 1 4 4-4 4m-6-4h10"/></svg>
                            Sair
                        </button>
                    </form>
                </div>
            </details>
        @endauth
    </div>
    @auth
        @if(auth()->user()->ativo && auth()->user()->eAdministrador())
            <nav class="header-admin-nav" aria-label="Navegação administrativa">
                <span class="header-admin-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6Z"/><path d="m8 12 3 3 5-6"/></svg>
                    Administração
                </span>
                <a href="{{ route('forms.index') }}" @if(request()->routeIs('forms.*', 'dashboard')) aria-current="page" @endif>Meus formulários</a>
                <a href="{{ route('administracao.usuarios.listar') }}" @if(request()->routeIs('administracao.usuarios.*')) aria-current="page" @endif>Gerenciar usuários</a>
                <a href="{{ route('administracao.formularios.listar') }}" @if(request()->routeIs('administracao.formularios.*')) aria-current="page" @endif>Todos os formulários</a>
            </nav>
        @endif
    @endauth
</header>
