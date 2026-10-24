<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnvioFormulario extends Model
{
    public function descricaoRespondente(): string
    {
        if (! $this->identificado) return 'Sem identificação';
        if ($this->usuario_id) return $this->usuario?->name ?? 'Usuário indisponível';

        return ($this->tipo_identificador === 'cpf' ? 'CPF: ' : 'E-mail: ').$this->identificador.' (informado, sem login)';
    }

    public static function usuarioJaRespondeu(Formulario $form, User $usuario): bool
    {
        $chave = \App\Support\IdentificadorResposta::chave('email', $usuario->email);

        return $form->envios()->where(fn ($query) => $query->where('chave_participante', $chave)
            ->orWhere(fn ($query) => $query->where('identificado', true)->where('usuario_id', $usuario->id)))->exists();
    }

    public $timestamps = false;

    protected $table = 'envios_formularios';

    protected function casts(): array
    {
        return ['identificado' => 'boolean', 'enviado_em' => 'datetime'];
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(RespostaFormulario::class, 'envio_id')->orderBy('ordem');
    }

    public function formulario(): BelongsTo
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(Formulario $formulario, array $dados, ?User $usuario): self
    {
        return DB::transaction(function () use ($formulario, $dados, $usuario) {
            $form = Formulario::whereKey($formulario->id)->lockForUpdate()->firstOrFail();
            if ($form->situacao !== 'publicado') {
                throw ValidationException::withMessages(['formulario' => 'Este formulário não está mais aceitando respostas.']);
            }
            if ($form->prazoEncerrado()) {
                throw ValidationException::withMessages(['formulario' => 'O prazo para responder a este formulário terminou. Novas respostas não são mais aceitas.']);
            }
            $usuarioLocal = $form->exigeLogin() && $usuario ? User::whereKey($usuario->id)->where('ativo', true)->first() : null;
            if ($form->exigeLogin() && ! $usuarioLocal) {
                throw ValidationException::withMessages(['formulario' => 'Faça login com um usuário cadastrado e ativo para responder.']);
            }
            $form->load('perguntas');
            if (! hash_equals($form->versaoRespostas(), $dados['versao'])) {
                throw ValidationException::withMessages(['formulario' => 'O formulário foi alterado. Revise as perguntas antes de enviar novamente.']);
            }
            $existente = static::where('chave_envio', $dados['chave_envio'])->first();
            if ($existente) {
                abort_unless($existente->formulario_id === $form->id, 409);
                abort_if($form->exigeLogin() && $existente->usuario_id !== $usuarioLocal->id, 409);

                return $existente;
            }
            $tipo = null;
            $identificador = null;
            $chaveParticipante = null;
            if ($form->exigeLogin()) {
                $tipo = 'email';
                $identificador = $usuarioLocal->email;
                if (static::usuarioJaRespondeu($form, $usuarioLocal)) {
                    throw ValidationException::withMessages(['formulario' => 'Você já respondeu a este formulário. É permitido somente um envio por usuário.']);
                }
            } elseif ($form->limitaPorIdentificador()) {
                $tipo = $form->tipo_identificador;
                $identificador = \App\Support\IdentificadorResposta::normalizar($tipo, $dados['identificador'] ?? null);
            }
            if ($tipo !== null) {
                $chaveParticipante = \App\Support\IdentificadorResposta::chave($tipo, $identificador);
                if ($form->envios()->where('chave_participante', $chaveParticipante)->exists()) {
                    throw ValidationException::withMessages(['identificador' => 'Já existe uma resposta para este CPF ou e-mail neste formulário.']);
                }
            }
            $envio = new self;
            $envio->formulario_id = $form->id;
            $envio->usuario_id = $usuarioLocal?->id;
            $envio->identificado = $tipo !== null;
            $envio->tipo_identificador = $tipo;
            $envio->identificador = $identificador;
            $envio->chave_participante = $chaveParticipante;
            $envio->chave_envio = $dados['chave_envio'];
            $envio->titulo_formulario = $form->titulo;
            $envio->enviado_em = now();
            $envio->save();
            foreach ($form->perguntas as $pergunta) {
                $valor = $dados['respostas'][$pergunta->id] ?? null;
                $envio->respostas()->create([
                    'pergunta_id' => $pergunta->id, 'titulo_pergunta' => $pergunta->titulo,
                    'tipo_pergunta' => $pergunta->tipo, 'obrigatoria' => $pergunta->obrigatoria,
                    'ordem' => $pergunta->ordem, 'opcoes' => $pergunta->opcoes,
                    'configuracao' => $pergunta->configuracao, 'valor' => $valor,
                ]);
            }

            return $envio;
        });
    }
}
