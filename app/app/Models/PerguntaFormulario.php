<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerguntaFormulario extends Model
{
    use \App\Models\Concerns\TemTituloFormatado;
    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $table = 'perguntas_formulario';

    public const SHORT_TEXT_LIMIT = 255;

    public const LONG_TEXT_LIMIT = 5000;

    public function answerCharacterLimit(): ?int
    {
        return match ($this->tipo) {
            'short_text' => self::SHORT_TEXT_LIMIT,
            'long_text' => self::LONG_TEXT_LIMIT,
            default => null,
        };
    }

    public const TYPES = [
        'short_text' => 'Resposta curta',
        'long_text' => 'Parágrafo',
        'radio' => 'Múltipla escolha',
        'checkbox' => 'Caixas de seleção',
        'select' => 'Lista suspensa',
        'escala_linear' => 'Escala linear',
        'classificacao' => 'Classificação',
        'grade_multipla' => 'Grade de múltipla escolha',
        'grade_caixas' => 'Grade da caixa de seleção',
        'data' => 'Data',
        'horario' => 'Horário',
    ];

    protected $fillable = ['titulo', 'tipo', 'obrigatoria', 'ordem', 'opcoes', 'configuracao'];

    protected function casts(): array
    {
        return ['obrigatoria' => 'boolean', 'opcoes' => 'array', 'configuracao' => 'array'];
    }

    public function formulario(): BelongsTo
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}
