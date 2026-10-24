<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespostaFormulario extends Model
{
    public $timestamps = false;

    protected $table = 'respostas_formularios';

    protected $fillable = ['pergunta_id', 'titulo_pergunta', 'tipo_pergunta', 'obrigatoria', 'ordem', 'opcoes', 'configuracao', 'valor'];

    protected function casts(): array
    {
        return ['obrigatoria' => 'boolean', 'opcoes' => 'array', 'configuracao' => 'array', 'valor' => 'json'];
    }

    public function envio(): BelongsTo
    {
        return $this->belongsTo(EnvioFormulario::class, 'envio_id');
    }

    public function valoresParaExibicao(): array
    {
        $valor = $this->valor;
        if ($valor === null || $valor === '' || $valor === []) {
            return [];
        }
        $opcoes = $this->opcoes ?? [];
        if (in_array($this->tipo_pergunta, ['radio', 'select'], true)) {
            return [$opcoes[$valor] ?? 'Opção indisponível'];
        }
        if ($this->tipo_pergunta === 'checkbox') {
            return array_map(fn ($indice) => $opcoes[$indice] ?? 'Opção indisponível', (array) $valor);
        }
        if ($this->tipo_pergunta === 'data') {
            return [\Illuminate\Support\Carbon::createFromFormat('Y-m-d', (string) $valor)->format('d/m/Y')];
        }
        if ($this->tipo_pergunta === 'classificacao') {
            return [$valor.' de '.($this->configuracao['maximo'] ?? 5).' estrelas'];
        }
        if ($this->tipo_pergunta === 'escala_linear') {
            return [$valor.' (escala de '.($this->configuracao['minimo'] ?? 1).' a '.($this->configuracao['maximo'] ?? 5).')'];
        }

        return is_scalar($valor) ? [(string) $valor] : [];
    }

    public function linhasDaGradeParaExibicao(): array
    {
        $linhas = [];
        $valores = is_array($this->valor) ? $this->valor : [];
        foreach ($this->configuracao['linhas'] ?? [] as $indice => $titulo) {
            $valor = $valores[$indice] ?? null;
            $selecoes = $this->tipo_pergunta === 'grade_caixas' ? (array) $valor : ($valor !== null && $valor !== '' ? [$valor] : []);
            $linhas[] = [
                'titulo' => $titulo,
                'respostas' => array_map(fn ($opcao) => $this->opcoes[$opcao] ?? 'Opção indisponível', $selecoes),
            ];
        }

        return $linhas;
    }
}
