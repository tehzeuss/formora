<section class="publication-panel" aria-labelledby="publication-title">
    <div class="publication-heading">
        <div><h2 id="publication-title">Publicação e compartilhamento</h2><p>Salve as alterações antes de publicar ou compartilhar.</p></div>
        @if($form->exists)
            @if($form->situacao === 'publicado')
                <form method="post" action="{{ route('formularios.encerrar', $form) }}" data-publication-action>
                    @csrf
                    <button type="submit" class="publication-close">Encerrar recebimento</button>
                </form>
            @else
                <form method="post" action="{{ route('formularios.publicar', $form) }}" data-publication-action>
                    @csrf
                    <button type="submit" class="button">{{ $form->situacao === 'encerrado' ? 'Reabrir formulário' : 'Publicar formulário' }}</button>
                </form>
            @endif
        @else
            <span class="publication-hint">Salve o formulário para publicar.</span>
        @endif
    </div>
    <div class="publication-deadline">
        <span class="deadline-title">Prazo de encerramento <span>(opcional)</span></span>
        <div class="deadline-fields">
            <div>
                <label for="data-encerramento">Data</label>
                <input id="data-encerramento" type="date" name="data_encerramento" form="form-builder" value="{{ old('data_encerramento', $form->prazo_encerramento?->copy()->timezone('America/Sao_Paulo')->format('Y-m-d')) }}" aria-describedby="deadline-help">
            </div>
            <div>
                <label for="hora-encerramento">Horário</label>
                <input id="hora-encerramento" type="time" name="hora_encerramento" form="form-builder" value="{{ old('hora_encerramento', $form->prazo_encerramento?->copy()->timezone('America/Sao_Paulo')->format('H:i')) }}" aria-describedby="deadline-help">
            </div>
        </div>
        <small id="deadline-help">Horário de Brasília. Ao atingir a data e o horário, o formulário deixa de aceitar respostas automaticamente. Deixe vazio para encerrar manualmente.</small>
        @if($form->situacao === 'publicado' && $form->prazoEncerrado())
            <p class="deadline-expired" role="status">O prazo terminou. Para voltar a receber respostas, salve um novo prazo futuro ou deixe o campo vazio.</p>
        @endif
    </div>
    @if($form->token_compartilhamento)
        <label class="share-label" for="share-link">Link de compartilhamento</label>
        <div class="share-link-row">
            <input id="share-link" type="text" value="{{ $form->linkCompartilhamento() }}" readonly>
            <button type="button" class="button" data-copy-share>Copiar link</button>
            <a href="{{ $form->linkCompartilhamento() }}" target="_blank" rel="noopener noreferrer">Abrir link <x-icone name="arrow-right" /></a>
        </div>
        <p class="copy-feedback" data-copy-feedback role="status" aria-live="polite"></p>
        @if($form->situacao === 'encerrado')
            <p class="publication-hint">O formulário está encerrado. Quem abrir o link verá um aviso de encerramento.</p>
        @endif
    @endif
</section>
