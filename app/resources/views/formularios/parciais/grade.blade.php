<div class="answer-grid-scroll" tabindex="0" role="region" aria-labelledby="question-label-{{ $question->id }}">
    <table class="answer-grid">
        <caption class="sr-only">{{ $question->titulo }} — {{ $question->tipo === 'grade_caixas' ? 'Selecione uma ou mais colunas por linha' : 'Selecione uma coluna por linha' }}</caption>
        <thead><tr><th scope="col"><span class="sr-only">Linha</span></th>@foreach($question->opcoes ?? [] as $coluna)<th scope="col">{{ $coluna }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach($question->configuracao['linhas'] ?? [] as $linha)
                @php
                    $indiceLinha = $loop->index;
                    $valorLinha = is_array($valorResposta) ? ($valorResposta[$indiceLinha] ?? null) : null;
                    $marcadosLinha = is_array($valorLinha) ? array_map(fn ($valor) => is_scalar($valor) ? (string) $valor : '', $valorLinha) : [];
                @endphp
                <tr @if($obrigatoria && $question->tipo === 'grade_caixas') data-required-choice @endif><th scope="row">{{ $linha }}</th>
                    @foreach($question->opcoes ?? [] as $coluna)
                        <td><input type="{{ $question->tipo === 'grade_caixas' ? 'checkbox' : 'radio' }}" name="{{ $campoResposta }}[{{ $indiceLinha }}]{{ $question->tipo === 'grade_caixas' ? '[]' : '' }}" value="{{ $loop->index }}" aria-label="{{ $linha }}: {{ $coluna }}" @checked($question->tipo === 'grade_caixas' ? in_array((string) $loop->index, $marcadosLinha, true) : (is_scalar($valorLinha) && (string) $valorLinha === (string) $loop->index)) @required($obrigatoria && $question->tipo === 'grade_multipla')></td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
