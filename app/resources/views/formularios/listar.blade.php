@extends('layouts.app')
@section('title', 'Meus formulários | Formora')
@push('styles') @vite('resources/css/paginas/formularios/listar.css') @endpush
@section('content')
<section class="forms-page">
    <div class="workspace-welcome">
        <div>
            <span class="workspace-kicker">SEU ESPAÇO DE CRIAÇÃO</span>
            <h1>Olá, {{ \Illuminate\Support\Str::before(trim(auth()->user()->name), ' ') }}.</h1>
            <p>Transforme perguntas em informações para sua equipe.<br>Crie, compartilhe e acompanhe tudo por aqui.</p>
            <a class="workspace-create" href="{{ route('forms.create') }}"><x-icone name="plus" /> Criar formulário</a>
        </div>
        <div class="workspace-illustration" aria-hidden="true">
            <svg width="200" height="170" viewBox="0 0 200 170" fill="none"><rect x="44" y="12" width="120" height="148" rx="12" fill="white" fill-opacity=".08" transform="rotate(8 104 86)"/><rect x="30" y="10" width="120" height="148" rx="12" fill="#f3f8fc"/><rect x="46" y="29" width="62" height="7" rx="3.5" fill="#294f6b"/><rect x="46" y="43" width="88" height="4" rx="2" fill="#becfdd"/><rect x="46" y="64" width="88" height="24" rx="4" stroke="#b4c9d9"/><circle cx="51" cy="105" r="5" stroke="#7999b0"/><path d="M65 105h57M65 124h42" stroke="#b4c9d9" stroke-width="4" stroke-linecap="round"/><circle cx="51" cy="124" r="5" stroke="#7999b0"/><circle cx="151" cy="133" r="21" fill="#81b7b1"/><path d="m142 133 6 6 12-13" stroke="#163e4c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </div>
    <dl class="workspace-stats" aria-label="Resumo dos seus formulários">
        <div><dt>Formulários</dt><dd>{{ $resumo['total'] }}</dd><span>Na sua área de trabalho</span></div>
        <div><dt>Publicados</dt><dd>{{ $resumo['publicados'] }}</dd><span>Disponíveis para responder</span></div>
        <div><dt>Rascunhos</dt><dd>{{ $resumo['rascunhos'] }}</dd><span>Em preparação</span></div>
        <div><dt>Respostas recebidas</dt><dd>{{ $resumo['respostas'] }}</dd><span>Nos seus formulários</span></div>
    </dl>
    <div class="forms-heading">
        <div><span class="eyebrow">ÁREA DE TRABALHO</span>
            <h2>Meus formulários</h2>
            <p>Crie e organize os formulários da sua rotina.</p>
        </div><a class="button" href="{{ route('forms.create') }}"><x-icone name="plus" /> Criar formulário</a>
    </div>
    @if(session('status'))<div class="forms-notice" role="status">{{ session('status') }}</div>@endif
    @if($forms->isEmpty())
    <div class="forms-empty"><span class="empty-icon" aria-hidden="true">▤</span>
        <h2>Seu primeiro formulário começa aqui.</h2>
        <p>Adicione perguntas, escolha os tipos de resposta e organize tudo do seu jeito.</p><a class="button" href="{{ route('forms.create') }}">Criar meu primeiro formulário</a>
    </div>
    @else
    <div class="forms-grid">
        <a class="workspace-new-card" href="{{ route('forms.create') }}">
            <span class="workspace-new-icon"><x-icone name="plus" :size="28" /></span>
            <strong>Uma nova ideia começa aqui</strong>
            <span>Crie um formulário em branco e adicione suas perguntas.</span>
            <span class="workspace-new-action">Criar formulário <x-icone name="arrow-right" /></span>
        </a>
        @foreach($forms as $form)
        <article class="form-card">
            <div class="form-card-cover" aria-hidden="true">
                @if($form->imagem)
                    <img src="{{ route('forms.imagem', $form) }}" alt="" loading="lazy">
                @else
                    <span>▤</span>
                @endif
            </div>
            <div class="form-card-content">
                <h2><a href="{{ route('forms.edit', $form) }}" title="{{ $form->titulo }}">{{ $form->titulo }}</a></h2>
                <p>{{ \Illuminate\Support\Str::limit($form->descricao ?? 'Sem descrição', 100) }}</p>
                <div class="form-card-meta"><span>{{ $form->perguntas_count }} {{ $form->perguntas_count === 1 ? 'pergunta' : 'perguntas' }}</span><span>{{ $form->rotuloSituacao() }}</span></div>
                <div class="form-card-bottom"><small>Atualizado em {{ $form->atualizado_em->format('d/m/Y') }}</small><a href="{{ route('forms.edit', $form) }}"><x-icone name="edit" /> Editar</a></div>
                <a class="form-responses-link" href="{{ route('formularios.respostas.listar', $form) }}">Respostas <span>{{ $form->envios_count }}</span><x-icone name="arrow-right" /></a>
                @include('formularios.parciais.acoes')
            </div>
        </article>
        @endforeach
    </div>
    <div class="forms-pagination">{{ $forms->links() }}</div>
    @endif
</section>
@endsection
