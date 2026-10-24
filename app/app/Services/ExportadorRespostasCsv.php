<?php

namespace App\Services;

use App\Models\Formulario;
use App\Models\RespostaFormulario;
use Illuminate\Support\Facades\DB;

class ExportadorRespostasCsv
{
    private function chave(RespostaFormulario $resposta): string
    {
        return hash('sha256', json_encode([
            $resposta->pergunta_id, $resposta->titulo_pergunta, $resposta->tipo_pergunta,
            $resposta->opcoes, $resposta->configuracao,
        ], JSON_THROW_ON_ERROR));
    }

    private function linha($arquivo, array $valores): void
    {
        $seguros = array_map(function ($valor) {
            $texto = (string) ($valor ?? '');
            // Evita que conteúdo livre seja interpretado como fórmula ao abrir a planilha.
            return preg_match('/^(?:[\t\r\n]|\s*[=+@-])/u', $texto) ? "'".$texto : $texto;
        }, $valores);
        if (fputcsv($arquivo, $seguros, ';', '"', '', "\r\n") === false) {
            throw new \RuntimeException('Não foi possível gerar o arquivo CSV.');
        }
    }

    public function escrever(Formulario $formulario, int $usuarioId, $arquivo): void
    {
        DB::transaction(function () use ($formulario, $usuarioId, $arquivo) {
            $form = Formulario::whereKey($formulario->id)->lockForUpdate()->firstOrFail();
            abort_unless($form->usuario_id === $usuarioId, 404);
            $limite = $form->envios()->max('id') ?? 0;
            $colunas = [];
            $consulta = RespostaFormulario::whereHas('envio', fn ($query) => $query->where('formulario_id', $form->id)->where('id', '<=', $limite));
            // Usa as perguntas salvas em cada envio, incluindo versões anteriores e perguntas removidas.
            (clone $consulta)->select(['id', 'pergunta_id', 'titulo_pergunta', 'tipo_pergunta', 'ordem', 'opcoes', 'configuracao'])
                ->chunkById(500, function ($respostas) use (&$colunas) {
                    foreach ($respostas as $resposta) {
                        $colunas[$this->chave($resposta)] ??= ['titulo' => $resposta->titulo_pergunta, 'ordem' => $resposta->ordem];
                    }
                });
            uasort($colunas, fn ($a, $b) => $a['ordem'] <=> $b['ordem']);
            $colunas = array_map(fn ($coluna) => $coluna['titulo'], $colunas);
            if ($colunas === []) {
                foreach ($form->perguntas()->get() as $pergunta) {
                    $resposta = new RespostaFormulario([
                        'pergunta_id' => $pergunta->id, 'titulo_pergunta' => $pergunta->titulo, 'tipo_pergunta' => $pergunta->tipo,
                        'opcoes' => $pergunta->opcoes, 'configuracao' => $pergunta->configuracao,
                    ]);
                    $colunas[$this->chave($resposta)] = $pergunta->titulo;
                }
            }
            $titulos = [];
            foreach ($colunas as $chave => $titulo) {
                $numero = ($titulos[$titulo] ?? 0) + 1;
                $titulos[$titulo] = $numero;
                $colunas[$chave] = $titulo.($numero > 1 ? ' (versão '.$numero.')' : '');
            }
            $identificado = $form->envios()->where('id', '<=', $limite)->where('identificado', true)->exists();
            fwrite($arquivo, "\xEF\xBB\xBF");
            $cabecalho = ['ID do envio', 'Enviado em', 'Identificação'];
            if ($identificado) {
                array_push($cabecalho, 'Nome', 'CPF', 'E-mail', 'Origem da identificação');
            }
            $this->linha($arquivo, [...$cabecalho, ...array_values($colunas)]);
            $form->envios()->where('id', '<=', $limite)->with(['usuario:id,name', 'respostas'])->chunkById(100, function ($envios) use ($arquivo, $colunas, $identificado) {
                foreach ($envios as $envio) {
                    $linha = [$envio->id, $envio->enviado_em->format('d/m/Y H:i:s'), $envio->identificado ? 'Identificado' : 'Anônimo'];
                    if ($identificado) {
                        $linha[] = $envio->identificado ? $envio->usuario?->name : '';
                        $linha[] = $envio->identificado && $envio->tipo_identificador === 'cpf' ? $envio->identificador : '';
                        $linha[] = $envio->identificado && $envio->tipo_identificador === 'email' ? $envio->identificador : '';
                        $linha[] = $envio->identificado ? ($envio->usuario_id ? 'Login da conta' : 'Informado sem login') : '';
                    }
                    $valores = array_fill_keys(array_keys($colunas), '');
                    foreach ($envio->respostas as $resposta) {
                        $valor = in_array($resposta->tipo_pergunta, ['grade_multipla', 'grade_caixas'], true)
                            ? implode(' | ', array_map(fn ($linha) => $linha['titulo'].': '.implode(', ', $linha['respostas']), $resposta->linhasDaGradeParaExibicao()))
                            : implode(' | ', $resposta->valoresParaExibicao());
                        $valores[$this->chave($resposta)] = $valor;
                    }
                    $this->linha($arquivo, [...$linha, ...array_values($valores)]);
                }
            });
        });
    }
}
