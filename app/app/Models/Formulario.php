<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formulario extends Model
{
    public function prazoEncerrado(): bool
    {
        return $this->prazo_encerramento !== null && $this->prazo_encerramento->lessThanOrEqualTo(now());
    }

    public function rotuloSituacao(): string
    {
        if ($this->situacao === 'encerrado') return 'Encerrado';
        if ($this->situacao === 'publicado') return $this->prazoEncerrado() ? 'Prazo encerrado' : 'Publicado';

        return 'Rascunho';
    }

    private static function contagemSituacoes(?int $usuarioId = null)
    {
        return static::when($usuarioId !== null, fn ($query) => $query->where('usuario_id', $usuarioId))
            ->selectRaw("CASE WHEN situacao = 'publicado' AND prazo_encerramento <= ? THEN 'encerrado' ELSE situacao END as situacao_efetiva, count(*) as quantidade", [now()])
            ->groupBy('situacao_efetiva')->pluck('quantidade', 'situacao_efetiva');
    }

    public const MODOS_ACESSO = ['fechado', 'aberto_limitado', 'aberto_livre'];

    public function exigeLogin(): bool
    {
        return $this->modo_acesso === 'fechado';
    }

    public function limitaPorIdentificador(): bool
    {
        return $this->modo_acesso === 'aberto_limitado';
    }

    use \App\Models\Concerns\GerenciaFormularios;

    public static function resumoAdministrativo(): array
    {
        $situacoes = static::contagemSituacoes();

        return [
            'total' => (int) $situacoes->sum(),
            'publicados' => (int) ($situacoes['publicado'] ?? 0),
            'rascunhos' => (int) ($situacoes['rascunho'] ?? 0),
            'encerrados' => (int) ($situacoes['encerrado'] ?? 0),
        ];
    }

    public static function resumoDoUsuario(int $usuarioId): array
    {
        $situacoes = static::contagemSituacoes($usuarioId);

        return [
            'total' => (int) $situacoes->sum(),
            'publicados' => (int) ($situacoes['publicado'] ?? 0),
            'rascunhos' => (int) ($situacoes['rascunho'] ?? 0),
            'respostas' => EnvioFormulario::whereHas('formulario', fn ($query) => $query->where('usuario_id', $usuarioId))->count(),
        ];
    }

    public function transferirPropriedade(User $administrador, int $destinoId, int $anteriorId): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($administrador, $destinoId, $anteriorId) {
            Perfil::whereKey(Perfil::ADMINISTRADOR)->lockForUpdate()->firstOrFail();
            $ator = User::findOrFail($administrador->id);
            abort_unless($ator->ativo && $ator->eAdministrador(), 403);
            $form = static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if ($form->usuario_id !== $anteriorId) {
                throw \Illuminate\Validation\ValidationException::withMessages(['usuario_id' => 'O proprietário mudou. Recarregue a página antes de transferir.']);
            }
            $destino = User::findOrFail($destinoId);
            if (! $destino->ativo || $destino->id === $form->usuario_id) {
                throw \Illuminate\Validation\ValidationException::withMessages(['usuario_id' => 'Selecione outro usuário local ativo.']);
            }
            TransferenciaFormulario::create([
                'formulario_id' => $form->id, 'administrador_id' => $ator->id,
                'proprietario_anterior_id' => $form->usuario_id, 'proprietario_novo_id' => $destino->id,
                'transferido_em' => now(),
            ]);
            $form->usuario_id = $destino->id;
            $form->save();
            $this->refresh();
        });
    }

    use \App\Models\Concerns\ResumeRespostas;
    use \App\Models\Concerns\TemTituloFormatado;
    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $table = 'formularios';

    protected $fillable = ['titulo', 'descricao'];

    protected function casts(): array
    {
        return ['exige_identificacao' => 'boolean', 'publicado_em' => 'datetime', 'encerrado_em' => 'datetime', 'prazo_encerramento' => 'datetime'];
    }

    public function publicar(): void
    {
        $proprietario = $this->usuario_id;
        \Illuminate\Support\Facades\DB::transaction(function () use ($proprietario) {
            static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            $this->refresh();
            abort_unless($this->usuario_id === $proprietario, 403);
            if ($this->prazoEncerrado()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['prazo_encerramento' => 'Atualize ou remova o prazo de encerramento antes de publicar ou reabrir.']);
            }
            if (! $this->perguntas()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['publicacao' => 'Adicione pelo menos uma pergunta antes de publicar.']);
            }
            $this->token_compartilhamento ??= \Illuminate\Support\Str::random(64);
            $this->situacao = 'publicado';
            $this->publicado_em ??= now();
            $this->encerrado_em = null;
            $this->save();
        });
    }

    public function encerrar(): void
    {
        $proprietario = $this->usuario_id;
        \Illuminate\Support\Facades\DB::transaction(function () use ($proprietario) {
            static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            $this->refresh();
            abort_unless($this->usuario_id === $proprietario, 403);
            if ($this->situacao !== 'publicado') {
                throw \Illuminate\Validation\ValidationException::withMessages(['publicacao' => 'Somente formulários publicados podem ser encerrados.']);
            }
            $this->situacao = 'encerrado';
            $this->encerrado_em = now();
            $this->save();
        });
    }

    public function linkCompartilhamento(): ?string
    {
        return $this->token_compartilhamento ? route('compartilhamento.exibir', $this->token_compartilhamento) : null;
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function perguntas(): HasMany
    {
        return $this->hasMany(PerguntaFormulario::class, 'formulario_id')->orderBy('ordem');
    }

    public function envios(): HasMany
    {
        return $this->hasMany(EnvioFormulario::class, 'formulario_id');
    }

    public function versaoRespostas(): string
    {
        $this->loadMissing('perguntas');

        return hash('sha256', json_encode([
            $this->titulo, $this->descricao, $this->modo_acesso, $this->tipo_identificador, $this->prazo_encerramento?->toIso8601String(),
            $this->perguntas->map(fn ($pergunta) => $pergunta->only(['id', 'titulo', 'tipo', 'obrigatoria', 'ordem', 'opcoes', 'configuracao'])),
        ], JSON_THROW_ON_ERROR));
    }

    public function salvarConteudo(array $dados): self
    {
        $proprietario = $this->usuario_id;
        $novos = [];
        $antigos = [];
        $atualizarImagem = function ($modelo, array $entrada) use (&$novos, &$antigos) {
            if (($entrada['imagem'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
                $caminho = $entrada['imagem']->store('formularios/'.$this->usuario_id, 'local');
                if (! $caminho) {
                    throw new \RuntimeException('Não foi possível armazenar a imagem.');
                }
                $novos[] = $caminho;
                if ($modelo->imagem) {
                    $antigos[] = $modelo->imagem;
                }
                $modelo->imagem = $caminho;
            } elseif (! empty($entrada['remover_imagem'])) {
                if ($modelo->imagem) {
                    $antigos[] = $modelo->imagem;
                }
                $modelo->imagem = null;
            }
        };
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($dados, $atualizarImagem, &$antigos, $proprietario) {
                if ($this->exists) {
                    static::whereKey($this->id)->lockForUpdate()->firstOrFail();
                    $this->refresh();
                    abort_unless($this->usuario_id === $proprietario, 403);
                }
                $this->titulo = $dados['title'];
                $this->descricao = $dados['description'] ?? null;
                $this->modo_acesso = $dados['modo_acesso'];
                $this->exige_identificacao = $this->exigeLogin();
                $this->tipo_identificador = $this->limitaPorIdentificador() ? $dados['tipo_identificador'] : null;
                $this->prazo_encerramento = empty($dados['prazo_encerramento']) ? null
                    : \Illuminate\Support\Carbon::createFromFormat('!Y-m-d\TH:i', $dados['prazo_encerramento'], 'America/Sao_Paulo')->utc();
                $this->titulo_formatado = \App\Support\TextoFormatado::limpar($dados['titulo_formatado'] ?? '');
                $atualizarImagem($this, $dados);
                $this->save();
                $manter = [];
                foreach (array_values($dados['questions']) as $ordem => $entrada) {
                    $pergunta = empty($entrada['id']) ? new PerguntaFormulario : $this->perguntas()->findOrFail($entrada['id']);
                    $pergunta->fill([
                        'titulo' => $entrada['title'], 'tipo' => $entrada['type'],
                        'obrigatoria' => $entrada['required'], 'ordem' => $ordem,
                        'opcoes' => in_array($entrada['type'], ['radio', 'checkbox', 'select', 'grade_multipla', 'grade_caixas'], true) ? array_values($entrada['options']) : null,
                        'configuracao' => in_array($entrada['type'], ['escala_linear', 'classificacao', 'grade_multipla', 'grade_caixas'], true) ? ($entrada['configuracao'] ?? null) : null,
                    ]);
                    $pergunta->titulo_formatado = \App\Support\TextoFormatado::limpar($entrada['titulo_formatado'] ?? '');
                    $atualizarImagem($pergunta, $entrada);
                    $this->perguntas()->save($pergunta);
                    $manter[] = $pergunta->id;
                }
                $excluidas = $this->perguntas()->whereNotIn('id', $manter);
                foreach ((clone $excluidas)->whereNotNull('imagem')->pluck('imagem') as $caminho) {
                    $antigos[] = $caminho;
                }
                $excluidas->delete();
            });
        } catch (\Throwable $erro) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($novos);
            throw $erro;
        }
        \Illuminate\Support\Facades\Storage::disk('local')->delete($antigos);

        return $this;
    }
}
