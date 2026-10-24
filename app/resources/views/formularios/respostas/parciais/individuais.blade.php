<div class="individual-navigation">
    <span>Resposta <strong>{{ min($envios->currentPage(), $envios->total()) }}</strong> de <strong>{{ $envios->total() }}</strong></span>
    <div>
        @if($envios->previousPageUrl())<a href="{{ $envios->previousPageUrl() }}" aria-label="Resposta anterior"><x-icone name="arrow-left" /></a>@endif
        @if($envios->nextPageUrl())<a href="{{ $envios->nextPageUrl() }}" aria-label="Próxima resposta"><x-icone name="arrow-right" /></a>@endif
    </div>
</div>
@forelse($envios as $envio)
    @include('formularios.respostas.parciais.envio')
@empty
    <div class="responses-empty"><p>Não há resposta nesta página.</p><a href="{{ route('formularios.respostas.listar', ['form' => $form, 'aba' => 'individual']) }}">Voltar à primeira resposta</a></div>
@endforelse
