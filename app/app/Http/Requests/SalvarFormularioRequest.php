<?php

namespace App\Http\Requests;

use App\Models\PerguntaFormulario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SalvarFormularioRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $data = $this->input('data_encerramento');
        $hora = $this->input('hora_encerramento');
        $this->merge(['prazo_encerramento' => is_string($data) && $data !== '' && is_string($hora) && $hora !== '' ? $data.'T'.$hora : null]);
        $formatado = $this->input('titulo_formatado');
        if (is_string($formatado) && $formatado !== '') {
            $seguro = \App\Support\TextoFormatado::limpar($formatado);
            $this->merge(['titulo_formatado' => $seguro, 'title' => trim(html_entity_decode(strip_tags($seguro), ENT_QUOTES, 'UTF-8'))]);
        }
        $questions = $this->input('questions');
        if (! is_array($questions)) {
            return;
        }
        foreach ($questions as &$question) {
            if (is_array($question) && is_string($question['titulo_formatado'] ?? null) && $question['titulo_formatado'] !== '') {
                $question['titulo_formatado'] = \App\Support\TextoFormatado::limpar($question['titulo_formatado']);
                $question['title'] = trim(html_entity_decode(strip_tags($question['titulo_formatado']), ENT_QUOTES, 'UTF-8'));
            }
            if (is_array($question) && is_string($question['configuracao'] ?? null)) {
                $question['configuracao'] = json_decode($question['configuracao'], true);
            }
        }
        unset($question);
        $this->merge(['questions' => $questions]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'prazo_encerramento' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'data_encerramento' => ['nullable', 'required_with:hora_encerramento', 'date_format:Y-m-d'],
            'hora_encerramento' => ['nullable', 'required_with:data_encerramento', 'date_format:H:i'],
            'modo_acesso' => ['required', Rule::in(\App\Models\Formulario::MODOS_ACESSO)],
            'tipo_identificador' => ['exclude_unless:modo_acesso,aberto_limitado', 'required', Rule::in(['cpf', 'email'])],
            'titulo_formatado' => ['nullable', 'string', 'max:10000'],
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'remover_imagem' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:100'],
            'questions.*' => ['array:id,title,type,required,options,configuracao,titulo_formatado,imagem,remover_imagem'],
            'questions.*.id' => ['nullable', 'integer', 'distinct'],
            'questions.*.title' => ['required', 'string', 'max:255'],
            'questions.*.titulo_formatado' => ['nullable', 'string', 'max:10000'],
            'questions.*.imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'questions.*.remover_imagem' => ['nullable', 'boolean'],
            'questions.*.type' => ['required', Rule::in(array_keys(PerguntaFormulario::TYPES))],
            'questions.*.required' => ['required', 'boolean'],
            'questions.*.options' => ['nullable', 'array', 'max:100'],
            'questions.*.options.*' => ['required', 'string', 'max:255'],
            'questions.*.configuracao' => ['nullable', 'array:minimo,maximo,rotulo_inicial,rotulo_final,linhas'],
            'questions.*.configuracao.minimo' => ['nullable', 'integer', Rule::in([0, 1])],
            'questions.*.configuracao.maximo' => ['nullable', 'integer', 'min:2', 'max:10'],
            'questions.*.configuracao.rotulo_inicial' => ['nullable', 'string', 'max:100'],
            'questions.*.configuracao.rotulo_final' => ['nullable', 'string', 'max:100'],
            'questions.*.configuracao.linhas' => ['nullable', 'array', 'min:1', 'max:50'],
            'questions.*.configuracao.linhas.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $prazo = $this->input('prazo_encerramento');
            if (is_string($prazo) && $prazo !== '' && ! $validator->errors()->has('prazo_encerramento')) {
                $anterior = $this->route('form')?->prazo_encerramento?->copy()->timezone('America/Sao_Paulo')->format('Y-m-d\TH:i');
                if ($prazo !== $anterior && \Illuminate\Support\Carbon::createFromFormat('!Y-m-d\TH:i', $prazo, 'America/Sao_Paulo')->lessThanOrEqualTo(now())) {
                    $validator->errors()->add('prazo_encerramento', 'Escolha uma data e horário futuros para encerrar o recebimento.');
                }
            }
            $questions = $this->input('questions', []);
            if (! is_array($questions)) {
                return;
            }
            foreach ($questions as $index => $question) {
                if (! is_array($question)) {
                    continue;
                }
                $type = $question['type'] ?? '';
                if (in_array($type, ['radio', 'checkbox', 'select', 'grade_multipla', 'grade_caixas'], true)) {
                    $options = $question['options'] ?? [];
                    if (! is_array($options) || count($options) < 2) {
                        $validator->errors()->add("questions.$index.options", 'Adicione pelo menos duas opções à pergunta '.($index + 1).'.');
                    } elseif (count(array_unique(array_map(fn ($value) => is_string($value) ? mb_strtolower(trim($value)) : '', $options))) !== count($options)) {
                        $validator->errors()->add("questions.$index.options", 'As opções da pergunta '.($index + 1).' devem ser diferentes.');
                    }
                }
                $config = is_array($question['configuracao'] ?? null) ? $question['configuracao'] : [];
                if (in_array($type, ['escala_linear', 'classificacao'], true) && ! isset($config['maximo'])) {
                    $validator->errors()->add("questions.$index.configuracao", 'Defina o limite da escala ou classificação da pergunta '.($index + 1).'.');
                }
                if ($type === 'escala_linear' && ! isset($config['minimo'])) {
                    $validator->errors()->add("questions.$index.configuracao", 'Defina o início da escala da pergunta '.($index + 1).'.');
                }
                if (in_array($type, ['grade_multipla', 'grade_caixas'], true)) {
                    $rows = $config['linhas'] ?? [];
                    if (! is_array($rows) || count($rows) === 0) {
                        $validator->errors()->add("questions.$index.configuracao.linhas", 'Adicione pelo menos uma linha à grade da pergunta '.($index + 1).'.');
                    } elseif (count(array_unique(array_map(fn ($value) => is_string($value) ? mb_strtolower(trim($value)) : '', $rows))) !== count($rows)) {
                        $validator->errors()->add("questions.$index.configuracao.linhas", 'As linhas da grade devem ser diferentes.');
                    }
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'prazo_encerramento.date_format' => 'Informe uma data e horário válidos para o encerramento.',
            'data_encerramento.required_with' => 'Informe a data de encerramento.',
            'data_encerramento.date_format' => 'Informe uma data válida para o encerramento.',
            'hora_encerramento.required_with' => 'Informe o horário de encerramento.',
            'hora_encerramento.date_format' => 'Informe um horário válido para o encerramento.',
            'imagem.image' => 'Selecione uma imagem válida para o cabeçalho.',
            'imagem.mimes' => 'Use imagens JPG, PNG ou WebP.',
            'imagem.max' => 'A imagem deve ter até 5 MB.',
            'imagem.uploaded' => 'Não foi possível enviar a imagem. Confira o tamanho do arquivo.',
            'questions.*.imagem.image' => 'Selecione uma imagem válida para a pergunta.',
            'questions.*.imagem.mimes' => 'Use imagens JPG, PNG ou WebP nas perguntas.',
            'questions.*.imagem.max' => 'Cada imagem deve ter até 5 MB.',
            'title.required' => 'Informe o título do formulário.',
            'modo_acesso.required' => 'Escolha o modo de acesso ao formulário.',
            'modo_acesso.in' => 'Selecione um modo de acesso válido.',
            'tipo_identificador.required' => 'Escolha se o limite será por CPF ou e-mail.',
            'tipo_identificador.in' => 'Selecione CPF ou e-mail para limitar os envios.',
            'title.max' => 'O título deve ter até 255 caracteres.',
            'description.max' => 'A descrição deve ter até 5.000 caracteres.',
            'questions.required' => 'Adicione pelo menos uma pergunta.',
            'questions.max' => 'O formulário pode ter até 100 perguntas.',
            'questions.*.title.required' => 'Preencha o título de todas as perguntas.',
            'questions.*.title.max' => 'Cada pergunta deve ter até 255 caracteres.',
            'questions.*.options.*.required' => 'Preencha todas as opções das perguntas.',
            'questions.*.options.*.max' => 'Cada opção deve ter até 255 caracteres.',
            'questions.*.type.in' => 'Selecione um tipo de pergunta válido.',
            'questions.*.configuracao.*.max' => 'A configuração da pergunta ultrapassa o limite permitido.',
            'questions.*.configuracao.linhas.*.required' => 'Preencha o texto de todas as linhas da grade.',
        ];
    }
}
