<?php

namespace App\Models\Concerns;

use App\Models\PerguntaFormulario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait GerenciaFormularios
{
    public function duplicarParaProprietario(int $usuarioId): static
    {
        $arquivosCriados = [];
        try {
            return DB::transaction(function () use ($usuarioId, &$arquivosCriados) {
                $origem = static::whereKey($this->id)->lockForUpdate()->firstOrFail();
                abort_unless($origem->usuario_id === $usuarioId, 404);
                $origem->load('perguntas');
                $copiarImagem = function (?string $caminho) use ($usuarioId, &$arquivosCriados): ?string {
                    if (! $caminho) {
                        return null;
                    }
                    $destino = 'formularios/'.$usuarioId.'/'.Str::uuid().'.'.pathinfo($caminho, PATHINFO_EXTENSION);
                    $arquivosCriados[] = $destino;
                    if (! Storage::disk('local')->copy($caminho, $destino)) {
                        throw ValidationException::withMessages(['duplicacao' => 'Não foi possível copiar uma imagem. Verifique as imagens do formulário e tente novamente.']);
                    }

                    return $destino;
                };
                $copia = $origem->replicate();
                $copia->titulo = Str::limit('Cópia de '.$origem->titulo, 255, '');
                $copia->titulo_formatado = mb_strlen('Cópia de '.$origem->titulo) <= 255
                    ? \App\Support\TextoFormatado::limpar('Cópia de '.$origem->tituloFormatadoSeguro())
                    : null;
                $copia->situacao = 'rascunho';
                $copia->token_compartilhamento = null;
                $copia->publicado_em = null;
                $copia->encerrado_em = null;
                $copia->prazo_encerramento = null;
                $copia->imagem = $copiarImagem($origem->imagem);
                $copia->unsetRelations();
                $copia->save();
                foreach ($origem->perguntas as $pergunta) {
                    $nova = $pergunta->replicate();
                    $nova->imagem = $copiarImagem($pergunta->imagem);
                    $copia->perguntas()->save($nova);
                }

                return $copia;
            });
        } catch (\Throwable $erro) {
            Storage::disk('local')->delete($arquivosCriados);
            throw $erro;
        }
    }

    public function excluirComConteudo(int $usuarioId): void
    {
        DB::transaction(function () use ($usuarioId) {
            $formulario = static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            abort_unless($formulario->usuario_id === $usuarioId, 404);
            $imagens = $formulario->perguntas()->whereNotNull('imagem')->pluck('imagem')->all();
            if ($formulario->imagem) {
                $imagens[] = $formulario->imagem;
            }
            $formulario->delete();
            DB::afterCommit(function () use ($imagens) {
                foreach (array_unique($imagens) as $imagem) {
                    if (! static::where('imagem', $imagem)->exists() && ! PerguntaFormulario::where('imagem', $imagem)->exists()) {
                        if (! Storage::disk('local')->delete($imagem)) {
                            logger()->warning('Não foi possível remover uma imagem de formulário excluído.');
                        }
                    }
                }
            });
        });
    }
}
