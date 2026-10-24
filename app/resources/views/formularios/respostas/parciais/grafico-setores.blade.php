@php
$cores = ['#3267b1', '#d9574d', '#e6a52c', '#43957c', '#8467b4', '#3c9ca9', '#cb6d9c', '#71879b'];
$segmentos = [];
$inicio = 0;
$totalGrafico = array_sum(array_column($grupo['categorias'], 'quantidade'));
foreach (array_values($grupo['categorias']) as $indice => $categoria) {
$fim = $inicio + ($totalGrafico ? $categoria['quantidade'] / $totalGrafico * 100 : 0);
if ($categoria['quantidade']) $segmentos[] = $cores[$indice % count($cores)].' '.$inicio.'% '.$fim.'%';
$inicio = $fim;
}
@endphp
<div class="pie-chart-layout">
    <div class="pie-chart" role="img" aria-label="Distribuição das respostas. Os valores estão na legenda ao lado." style="background: {{ count($segmentos) ? 'conic-gradient('.implode(', ', $segmentos).')' : '#e4ebf2' }}"></div>
    <ul class="chart-legend">
        @foreach(array_values($grupo['categorias']) as $indice => $categoria)
        <li><span class="legend-dot" style="background: {{ $cores[$indice % count($cores)] }}" aria-hidden="true"></span><span>{{ $categoria['rotulo'] }}</span><strong>{{ $categoria['quantidade'] }} <small>({{ number_format($totalGrafico ? $categoria['quantidade'] / $totalGrafico * 100 : 0, 1, ',', '.') }}%)</small></strong></li>
        @endforeach
    </ul>
</div>