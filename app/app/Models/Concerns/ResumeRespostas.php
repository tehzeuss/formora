<?php

namespace App\Models\Concerns;

use App\Models\RespostaFormulario;

trait ResumeRespostas
{
    public function resumoRespostas(): array
    {
        $grupos = [];
        $chave = fn ($id, $titulo, $tipo, $opcoes, $configuracao) => hash('sha256', json_encode([$id, $titulo, $tipo, $opcoes, $configuracao], JSON_THROW_ON_ERROR));
        $criarGrupo = function ($id, $titulo, $tipo, $opcoes, $config, $atual = false) {
            $categorias = [];
            if (in_array($tipo, ['radio', 'select', 'checkbox', 'grade_multipla', 'grade_caixas'], true)) {
                foreach ($opcoes ?? [] as $indice => $rotulo) {
                    $categorias[$indice] = ['rotulo' => $rotulo, 'quantidade' => 0];
                }
            } elseif (in_array($tipo, ['escala_linear', 'classificacao'], true)) {
                $inicio = $tipo === 'classificacao' ? 1 : ($config['minimo'] ?? 1);
                for ($valor = $inicio; $valor <= ($config['maximo'] ?? 5); $valor++) {
                    $categorias[$valor] = ['rotulo' => (string) $valor, 'quantidade' => 0];
                }
            }
            $linhas = [];
            foreach ($config['linhas'] ?? [] as $rotulo) {
                $linhas[] = ['rotulo' => $rotulo, 'respondidas' => 0, 'categorias' => $categorias];
            }

            return ['titulo' => $titulo, 'tipo' => $tipo, 'atual' => $atual, 'respondidas' => 0, 'em_branco' => 0,
                'categorias' => $categorias, 'linhas' => $linhas, 'textos' => [], 'soma' => 0, 'configuracao' => $config];
        };
        foreach ($this->perguntas()->get() as $pergunta) {
            $id = $chave($pergunta->id, $pergunta->titulo, $pergunta->tipo, $pergunta->opcoes, $pergunta->configuracao);
            $grupos[$id] = $criarGrupo($pergunta->id, $pergunta->titulo, $pergunta->tipo, $pergunta->opcoes, $pergunta->configuracao ?? [], true);
        }
        RespostaFormulario::whereHas('envio', fn ($query) => $query->where('formulario_id', $this->id))
            ->orderBy('id')->chunkById(500, function ($respostas) use (&$grupos, $chave, $criarGrupo) {
                foreach ($respostas as $resposta) {
                    $id = $chave($resposta->pergunta_id, $resposta->titulo_pergunta, $resposta->tipo_pergunta, $resposta->opcoes, $resposta->configuracao);
                    $grupos[$id] ??= $criarGrupo($resposta->pergunta_id, $resposta->titulo_pergunta, $resposta->tipo_pergunta, $resposta->opcoes, $resposta->configuracao ?? []);
                    $grupo = &$grupos[$id];
                    $valor = $resposta->valor;
                    $temValor = function ($valor) use (&$temValor): bool {
                        if (is_array($valor)) {
                            foreach ($valor as $item) {
                                if ($temValor($item)) return true;
                            }
                            return false;
                        }
                        return $valor !== null && $valor !== '';
                    };
                    if (! $temValor($valor)) {
                        $grupo['em_branco']++;
                        unset($grupo);
                        continue;
                    }
                    $grupo['respondidas']++;
                    $tipo = $resposta->tipo_pergunta;
                    if (in_array($tipo, ['grade_multipla', 'grade_caixas'], true)) {
                        foreach ($grupo['linhas'] as $indice => &$linha) {
                            $respostaLinha = $valor[$indice] ?? null;
                            if (! $temValor($respostaLinha)) continue;
                            $linha['respondidas']++;
                            $selecoes = $tipo === 'grade_caixas' ? (array) $respostaLinha : [$respostaLinha];
                            foreach ($selecoes as $opcao) {
                                if (isset($linha['categorias'][$opcao])) $linha['categorias'][$opcao]['quantidade']++;
                            }
                        }
                        unset($linha);
                    } elseif ($tipo === 'checkbox') {
                        foreach ((array) $valor as $opcao) {
                            if (isset($grupo['categorias'][$opcao])) $grupo['categorias'][$opcao]['quantidade']++;
                        }
                    } elseif (in_array($tipo, ['radio', 'select', 'escala_linear', 'classificacao'], true)) {
                        if (isset($grupo['categorias'][$valor])) $grupo['categorias'][$valor]['quantidade']++;
                        if (in_array($tipo, ['escala_linear', 'classificacao'], true)) $grupo['soma'] += (int) $valor;
                    } elseif (count($grupo['textos']) < 100) {
                        $grupo['textos'][] = $resposta->valoresParaExibicao()[0] ?? '';
                    }
                    unset($grupo);
                }
            });

        return $grupos;
    }
}
