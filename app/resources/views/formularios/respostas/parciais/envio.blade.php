    <div class="response-summary">
        <span class="eyebrow">ENVIO #{{ $envio->id }}</span>
        <h1>{{ $envio->titulo_formulario }}</h1>
        <dl><div><dt>Recebido em</dt><dd>{{ $envio->enviado_em->timezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }} · Brasília</dd></div><div><dt>Respondente</dt><dd>{{ $envio->descricaoRespondente() }}</dd></div></dl>
        <p>As perguntas e opções abaixo correspondem ao momento do envio.</p>
    </div>
    @foreach($envio->respostas as $resposta)
        <article class="saved-answer">
            <div class="saved-answer-meta"><span>Pergunta {{ $loop->iteration }}</span><span>{{ \App\Models\PerguntaFormulario::TYPES[$resposta->tipo_pergunta] ?? 'Pergunta' }}{{ $resposta->obrigatoria ? ' · Obrigatória' : '' }}</span></div>
            <h2>{{ $resposta->titulo_pergunta }}</h2>
            @if(in_array($resposta->tipo_pergunta, ['grade_multipla', 'grade_caixas']))
                <dl class="saved-grid">
                    @foreach($resposta->linhasDaGradeParaExibicao() as $linha)
                        <div><dt>{{ $linha['titulo'] }}</dt><dd>{{ count($linha['respostas']) ? implode(', ', $linha['respostas']) : 'Não respondido' }}</dd></div>
                    @endforeach
                </dl>
            @else
                @php($valores = $resposta->valoresParaExibicao())
                @if(count($valores))
                    <ul class="saved-answer-values">@foreach($valores as $valor)<li>{{ $valor }}</li>@endforeach</ul>
                @else
                    <p class="unanswered">Não respondido.</p>
                @endif
            @endif
        </article>
    @endforeach
