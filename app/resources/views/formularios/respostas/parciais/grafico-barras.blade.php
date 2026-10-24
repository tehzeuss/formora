@php
$maximo = max([1, ...array_column($categorias, 'quantidade')]);
@endphp
<div class="{{ $vertical ? 'vertical-chart' : 'horizontal-chart' }}" role="group" aria-label="Distribuição das respostas">
    @foreach($categorias as $categoria)
    @php
    $percentual = $denominador ? $categoria['quantidade'] / $denominador * 100 : 0;
    $tamanho = $categoria['quantidade'] / $maximo * 100;
    @endphp
    @if($vertical)
    <div class="vertical-chart-column"><span class="bar-value">{{ $categoria['quantidade'] }} <small>({{ number_format($percentual, 1, ',', '.') }}%)</small></span>
        <div class="vertical-bar-track">
            <div class="vertical-bar-fill" style="height: {{ $tamanho }}%" aria-hidden="true"></div>
        </div><span class="bar-label">{{ $categoria['rotulo'] }}</span>
    </div>
    @else
    <div class="horizontal-chart-row"><span class="bar-label">{{ $categoria['rotulo'] }}</span>
        <div class="horizontal-bar-track">
            <div class="horizontal-bar-fill" style="width: {{ $tamanho }}%" aria-hidden="true"></div>
        </div><span class="bar-value">{{ $categoria['quantidade'] }} <small>({{ number_format($percentual, 1, ',', '.') }}%)</small></span>
    </div>
    @endif
    @endforeach
</div>