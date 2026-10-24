<article class="question-summary">
    <div class="question-summary-heading"><h2>{{ $grupo['titulo'] }}</h2><span>{{ \App\Models\PerguntaFormulario::TYPES[$grupo['tipo']] ?? 'Pergunta' }}</span></div>
    <p class="question-response-count">
        {{ $grupo['respondidas'] }} {{ $grupo['respondidas'] === 1 ? 'resposta' : 'respostas' }}
        @if($grupo['em_branco'])
            · {{ $grupo['em_branco'] }} em branco
        @endif
    </p>
    @if(!$grupo['atual'])<p class="previous-version-note">Versão anterior da pergunta. As opções foram preservadas como estavam no envio.</p>@endif
    @if($grupo['respondidas'] === 0)
        <p class="chart-empty">Nenhuma resposta para esta pergunta.</p>
    @elseif(in_array($grupo['tipo'], ['radio', 'select']))
        @include('formularios.respostas.parciais.grafico-setores')
    @elseif(in_array($grupo['tipo'], ['escala_linear', 'classificacao']))
        <div class="rating-average"><strong>{{ number_format($grupo['soma'] / $grupo['respondidas'], 2, ',', '.') }}</strong><span>Média das respostas</span></div>
        @include('formularios.respostas.parciais.grafico-barras', ['categorias' => $grupo['categorias'], 'denominador' => $grupo['respondidas'], 'vertical' => true])
    @elseif($grupo['tipo'] === 'checkbox')
        <p class="chart-caption">Cada pessoa pode selecionar mais de uma opção.</p>
        @include('formularios.respostas.parciais.grafico-barras', ['categorias' => $grupo['categorias'], 'denominador' => $grupo['respondidas'], 'vertical' => false])
    @elseif(in_array($grupo['tipo'], ['grade_multipla', 'grade_caixas']))
        @if($grupo['tipo'] === 'grade_caixas')<p class="chart-caption">Mais de uma opção pode ser selecionada por linha.</p>@endif
        @foreach($grupo['linhas'] as $linha)
            <div class="grid-summary-row"><h3>{{ $linha['rotulo'] }} <small>{{ $linha['respondidas'] }} respostas</small></h3>
                @include('formularios.respostas.parciais.grafico-barras', ['categorias' => $linha['categorias'], 'denominador' => $linha['respondidas'], 'vertical' => false])
            </div>
        @endforeach
    @else
        <ol class="text-responses">@foreach($grupo['textos'] as $texto)<li>{{ $texto }}</li>@endforeach</ol>
        @if($grupo['respondidas'] > count($grupo['textos']))<p class="chart-caption">Exibindo as primeiras {{ count($grupo['textos']) }} respostas. Consulte os demais envios na aba Individual.</p>@endif
    @endif
</article>
