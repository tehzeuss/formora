<?php

namespace Database\Seeders;

use App\Models\EnvioFormulario;
use App\Models\Formulario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemonstracaoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new \RuntimeException('A demonstração só pode ser criada no ambiente local.');
        }
        $usuario = User::where('ativo', true)->orderBy('id')->first();
        if (! $usuario) {
            throw new \RuntimeException('Crie uma conta pelo navegador ou execute php artisan formora:admin antes da demonstração.');
        }
        if (Formulario::where('usuario_id', $usuario->id)->where('titulo', 'Como foi sua experiência?')->exists()) return;
        DB::transaction(function () use ($usuario) {
            $form = new Formulario;
            $form->usuario_id = $usuario->id;
            $form->salvarConteudo([
                'title' => 'Como foi sua experiência?',
                'description' => 'Exemplo com dados fictícios para explorar o editor e os gráficos do Formora.',
                'modo_acesso' => 'aberto_livre',
                'questions' => [
                    ['title' => 'Como você conheceu o produto?', 'type' => 'radio', 'required' => true, 'options' => ['Busca na internet', 'Redes sociais', 'Indicação']],
                    ['title' => 'Quais recursos você mais utiliza?', 'type' => 'checkbox', 'required' => false, 'options' => ['Editor', 'Compartilhamento', 'Gráficos', 'Exportação']],
                    ['title' => 'Qual nota você dá à experiência?', 'type' => 'escala_linear', 'required' => true, 'options' => [], 'configuracao' => ['minimo' => 1, 'maximo' => 5, 'rotulo_inicial' => 'Pode melhorar', 'rotulo_final' => 'Excelente']],
                    ['title' => 'O que podemos melhorar?', 'type' => 'long_text', 'required' => false, 'options' => []],
                ],
            ]);
            $form->publicar();
            $form->load('perguntas');
            $sugestoes = ['Gostei da organização dos gráficos.', 'Seria útil ter mais temas de cores.', 'O editor é simples de usar.', 'A exportação facilitou a análise.'];
            for ($i = 0; $i < 16; $i++) {
                $respostas = [];
                foreach ($form->perguntas as $q) {
                    $respostas[$q->id] = match ($q->tipo) {
                        'radio' => (string) ($i % 3),
                        'checkbox' => [(string) ($i % 4), (string) (($i + 1) % 4)],
                        'escala_linear' => (string) (1 + $i % 5),
                        default => '[Demonstração] '.$sugestoes[$i % 4],
                    };
                }
                EnvioFormulario::registrar($form, ['versao' => $form->versaoRespostas(), 'chave_envio' => (string) Str::uuid(), 'respostas' => $respostas], null);
            }
        });
    }
}
