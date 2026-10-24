@php
    $rotaImagem = !empty($administrativo) ? 'administracao.formularios.imagem' : (!empty($compartilhado) ? 'compartilhamento.imagem' : 'forms.imagem');
    $parametrosImagem = !empty($compartilhado) ? ['token' => $form->token_compartilhamento] : ['form' => $form];
    $respondendo = $respondendo ?? false;
    $respostasPreenchidas = $respondendo ? old('respostas', []) : [];
    $respostasPreenchidas = is_array($respostasPreenchidas) ? $respostasPreenchidas : [];
@endphp
<section class="preview-heading">
    <h1 class="formatted-text">{!! $form->tituloFormatadoSeguro() !!}</h1>
    @if($form->imagem)
        <img class="form-cover-image" src="{{ route($rotaImagem, $parametrosImagem) }}" alt="Imagem do cabeçalho do formulário">
    @endif
    @if($form->descricao)
        <p>{{ $form->descricao }}</p>
    @endif
    <p class="required-explanation">* Pergunta obrigatória</p>
</section>
@foreach($form->perguntas as $question)
    @php
        $valorResposta = $respostasPreenchidas[$question->id] ?? null;
        $valorSimples = is_scalar($valorResposta) ? (string) $valorResposta : '';
        $valoresMarcados = is_array($valorResposta) ? array_map(fn ($valor) => is_scalar($valor) ? (string) $valor : '', $valorResposta) : [];
        $campoResposta = 'respostas['.$question->id.']';
        $campoErro = 'respostas.'.$question->id;
        $obrigatoria = $respondendo && $question->obrigatoria;
        $temErro = $respondendo && ($errors->has($campoErro) || $errors->has($campoErro.'.*'));
    @endphp
    <section class="preview-question {{ $temErro ? 'has-answer-error' : '' }}">
        <div class="preview-question-title formatted-text" id="question-label-{{ $question->id }}">
            {{ $loop->iteration }}. {!! $question->tituloFormatadoSeguro() !!}
            @if($question->obrigatoria)<span class="required-star">*</span>@endif
        </div>
        @if($question->imagem)
            <img class="question-image" src="{{ route($rotaImagem, array_merge($parametrosImagem, ['pergunta' => $question])) }}" alt="Imagem da pergunta {{ $loop->iteration }}" loading="lazy">
        @endif
        @if($question->tipo === 'short_text')
            <input id="answer-{{ $question->id }}" name="{{ $campoResposta }}" value="{{ $valorSimples }}" type="text" placeholder="Sua resposta" maxlength="{{ $question->answerCharacterLimit() }}" aria-labelledby="question-label-{{ $question->id }}" aria-describedby="counter-{{ $question->id }}" data-character-limit @required($obrigatoria)>
        @elseif($question->tipo === 'long_text')
            <textarea id="answer-{{ $question->id }}" name="{{ $campoResposta }}" rows="4" placeholder="Sua resposta" maxlength="{{ $question->answerCharacterLimit() }}" aria-labelledby="question-label-{{ $question->id }}" aria-describedby="counter-{{ $question->id }}" data-character-limit @required($obrigatoria)>{{ $valorSimples }}</textarea>
        @elseif($question->tipo === 'select')
            <select id="answer-{{ $question->id }}" name="{{ $campoResposta }}" aria-labelledby="question-label-{{ $question->id }}" @required($obrigatoria)>
                <option value="">Escolha uma opção</option>
                @foreach($question->opcoes ?? [] as $indice => $option)
                    <option value="{{ $indice }}" @selected($valorSimples !== '' && $valorSimples === (string) $indice)>{{ $option }}</option>
                @endforeach
            </select>
        @elseif($question->tipo === 'data')
            <input id="answer-{{ $question->id }}" name="{{ $campoResposta }}" value="{{ $valorSimples }}" type="date" aria-labelledby="question-label-{{ $question->id }}" @required($obrigatoria)>
        @elseif($question->tipo === 'horario')
            <input id="answer-{{ $question->id }}" name="{{ $campoResposta }}" value="{{ $valorSimples }}" type="time" aria-labelledby="question-label-{{ $question->id }}" @required($obrigatoria)>
        @elseif(in_array($question->tipo, ['escala_linear', 'classificacao']))
            @include('formularios.parciais.escala')
        @elseif(in_array($question->tipo, ['grade_multipla', 'grade_caixas']))
            @include('formularios.parciais.grade')
        @else
            <div class="preview-options" role="group" aria-labelledby="question-label-{{ $question->id }}" @if($obrigatoria && $question->tipo === 'checkbox') data-required-choice @endif>
                @foreach($question->opcoes ?? [] as $indice => $option)
                    <label><input type="{{ $question->tipo === 'checkbox' ? 'checkbox' : 'radio' }}" name="{{ $campoResposta }}{{ $question->tipo === 'checkbox' ? '[]' : '' }}" value="{{ $indice }}" @checked($question->tipo === 'checkbox' ? in_array((string) $indice, $valoresMarcados, true) : ($valorSimples !== '' && $valorSimples === (string) $indice)) @required($obrigatoria && $question->tipo === 'radio')> {{ $option }}</label>
                @endforeach
            </div>
        @endif
        @if($question->answerCharacterLimit())
            <div class="answer-counter" id="counter-{{ $question->id }}"><span data-character-count>0</span> / {{ number_format($question->answerCharacterLimit(), 0, ',', '.') }} caracteres</div>
        @endif
        @if($temErro)
            <p class="answer-error">{{ $errors->first($campoErro) ?: 'Confira as respostas desta pergunta.' }}</p>
        @endif
    </section>
@endforeach
