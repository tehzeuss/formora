@php
$initialQuestions = old('questions', $form->exists ? $form->perguntas->map(fn ($question) => ['id' => $question->id, 'title' => $question->titulo, 'type' => $question->tipo, 'required' => $question->obrigatoria, 'options' => $question->opcoes, 'configuracao' => $question->configuracao, 'titulo_formatado' => $question->titulo_formatado, 'imagem_url' => $question->imagem ? route('forms.imagem', ['form' => $form, 'pergunta' => $question]) : null])->toArray() : [['title' => '', 'type' => 'short_text', 'required' => false, 'options' => []]]);
if ($form->exists) {
    $initialQuestions = collect($initialQuestions)->map(function ($entrada) use ($form) {
        $pergunta = $form->perguntas->firstWhere('id', $entrada['id'] ?? null);
        $entrada['imagem_url'] = $pergunta && $pergunta->imagem && empty($entrada['remover_imagem']) ? route('forms.imagem', ['form' => $form, 'pergunta' => $pergunta]) : null;
        return $entrada;
    })->toArray();
}
@endphp
<div class="form-editor" data-form-editor data-remove-form-image="{{ old('remover_imagem', 0) }}" data-form-image-url="{{ $form->exists && $form->imagem && !old('remover_imagem') ? route('forms.imagem', $form) : '' }}" data-short-limit="{{ \App\Models\PerguntaFormulario::SHORT_TEXT_LIMIT }}" data-long-limit="{{ \App\Models\PerguntaFormulario::LONG_TEXT_LIMIT }}">
    <div class="editor-toolbar"><a href="{{ route('dashboard') }}"><x-icone name="arrow-left" /> Meus formulários</a>
        <div><span class="draft-badge">{{ $form->rotuloSituacao() }}</span>@if($form->exists)<a class="preview-link" href="{{ route('formularios.respostas.listar', $form) }}">Respostas</a><a class="preview-link" href="{{ route('forms.show', $form) }}">Visualizar</a>@endif<button class="button" type="submit" form="form-builder">Salvar formulário</button></div>
    </div>
    @if(session('status'))<div class="editor-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="editor-errors" role="alert"><strong>Confira os campos antes de salvar.</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>@endif
    @include('formularios.parciais.identificacao')
    @include('formularios.parciais.publicacao')
    @if($form->exists)
        @include('formularios.parciais.acoes')
    @endif
    <form id="form-builder" method="post" enctype="multipart/form-data" action="{{ $form->exists ? route('forms.update', $form) : route('forms.store') }}">
        @csrf
        @if($form->exists) @method('PUT') @endif
        <section class="editor-heading"><span class="editor-kicker">FORMULÁRIO</span><label class="sr-only" for="form-title">Título do formulário</label><input id="form-title" name="title" maxlength="255" value="{{ old('title', $form->titulo) }}" placeholder="Formulário sem título" required><div data-form-image></div><label class="sr-only" for="form-description">Descrição do formulário</label><textarea id="form-description" name="description" maxlength="5000" rows="2" placeholder="Descrição do formulário (opcional)">{{ old('description', $form->descricao) }}</textarea></section>

        <div data-question-list></div>
        <button class="add-question" type="button" data-add-question><x-icone name="plus" /> Adicionar pergunta</button>
        <p class="editor-footnote">Salve suas alterações antes de sair. A publicação utiliza as alterações salvas.</p>
        <noscript>
            <p>Ative o JavaScript do navegador para adicionar e editar as perguntas.</p>
        </noscript>
    </form>
    <p class="sr-only" role="status" aria-live="polite" data-editor-status></p>
    <script type="application/json" data-initial-title>{!! json_encode(old('titulo_formatado', $form->titulo_formatado), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script type="application/json" data-initial-questions>{!! json_encode($initialQuestions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <template data-question-template>
        <section class="question-card">
            <div class="question-top"><span class="question-number"></span>
                <div class="question-actions"><button type="button" data-move-up aria-label="Mover pergunta para cima"><x-icone name="arrow-up" /></button><button type="button" data-move-down aria-label="Mover pergunta para baixo"><x-icone name="arrow-down" /></button><button type="button" data-duplicate>Duplicar</button><button type="button" data-remove-question aria-label="Excluir pergunta" title="Excluir pergunta"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 6h18M9 6V4h6v2M5 6l1 14h12l1-14M10 10v6M14 10v6" />
                        </svg></button></div>
            </div>
            <div class="question-title-row"><label class="question-title-label">Pergunta<input data-question-title placeholder="Digite sua pergunta" maxlength="255" required></label><label class="question-type-label">Tipo de resposta<select data-question-type>@foreach($types as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label></div>
            <div data-question-image></div>
            <div class="answer-hint" data-answer-hint aria-hidden="true"></div>
            <div data-options-panel>
                <div data-options-list></div><button class="add-option" type="button" data-add-option><x-icone name="plus" /> Adicionar opção</button>
            </div>
            <div class="question-bottom"><label><input type="checkbox" data-question-required> Obrigatória</label></div>
        </section>
    </template>
</div>
