@php
    $configuracao = $question->configuracao ?? [];
    $estrelas = $question->tipo === 'classificacao';
    $minimo = $estrelas ? 1 : ($configuracao['minimo'] ?? 1);
    $maximo = $configuracao['maximo'] ?? 5;
@endphp
<div class="answer-scale {{ $estrelas ? 'answer-rating' : '' }}" role="radiogroup" aria-labelledby="question-label-{{ $question->id }}">
    @for($valor = $minimo; $valor <= $maximo; $valor++)
        <label class="scale-choice">
            <input type="radio" name="{{ $campoResposta }}" value="{{ $valor }}" aria-label="{{ $valor }}{{ $estrelas ? ' estrelas' : '' }}" @checked($valorSimples !== '' && $valorSimples === (string) $valor) @required($obrigatoria)>
            @if($estrelas)
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m12 3 2.8 5.7 6.3.9-4.6 4.5 1.1 6.3-5.6-3-5.6 3 1.1-6.3L3 9.6l6.2-.9Z"/></svg>
            @endif
            <span>{{ $valor }}</span>
        </label>
    @endfor
</div>
@if(! $estrelas)
    <div class="scale-labels"><span>{{ $configuracao['rotulo_inicial'] ?? '' }}</span><span>{{ $configuracao['rotulo_final'] ?? '' }}</span></div>
@endif
