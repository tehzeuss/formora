<?php

namespace App\Http\Requests;

use App\Models\Formulario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class EnviarRespostasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $form = $this->formulario();

        return $form->situacao === 'publicado'
            && ! $form->prazoEncerrado()
            && (! $form->exigeLogin() || $this->user()?->ativo === true);
    }

    public function formulario(): Formulario
    {
        return $this->attributes->get('formulario_compartilhado');
    }

    public function rules(): array
    {
        $form = $this->formulario()->loadMissing('perguntas');
        $ids = $form->perguntas->pluck('id')->all();
        $rules = [
            'versao' => ['required', 'string', 'size:64'],
            'chave_envio' => ['required', 'uuid', Rule::in((array) $this->session()->get('chaves_envio.'.$form->id, []))],
            'respostas' => ['nullable', 'array:'.implode(',', $ids)],
            'identificador' => $form->limitaPorIdentificador() ? ['required', 'string', 'max:255', function ($atributo, $valor, $falhar) use ($form) {
                try {
                    \App\Support\IdentificadorResposta::normalizar($form->tipo_identificador, $valor);
                } catch (\Illuminate\Validation\ValidationException $erro) {
                    $falhar($erro->errors()['identificador'][0]);
                }
            }] : ['exclude'],
        ];
        foreach ($form->perguntas as $pergunta) {
            $key = 'respostas.'.$pergunta->id;
            $required = $pergunta->obrigatoria ? 'required' : 'nullable';
            $opcoes = array_keys($pergunta->opcoes ?? []);
            $config = $pergunta->configuracao ?? [];
            switch ($pergunta->tipo) {
                case 'short_text':
                case 'long_text':
                    $rules[$key] = [$required, 'string', 'max:'.$pergunta->answerCharacterLimit()];
                    break;
                case 'radio':
                case 'select':
                    $rules[$key] = [$required, 'integer', Rule::in($opcoes)];
                    break;
                case 'checkbox':
                    $rules[$key] = [$required, 'array', 'min:1', 'max:'.count($opcoes)];
                    $rules[$key.'.*'] = ['required', 'integer', 'distinct', Rule::in($opcoes)];
                    break;
                case 'escala_linear':
                case 'classificacao':
                    $min = $pergunta->tipo === 'classificacao' ? 1 : ($config['minimo'] ?? 1);
                    $rules[$key] = [$required, 'integer', 'between:'.$min.','.($config['maximo'] ?? 5)];
                    break;
                case 'data':
                    $rules[$key] = [$required, 'date_format:Y-m-d'];
                    break;
                case 'horario':
                    $rules[$key] = [$required, 'date_format:H:i'];
                    break;
                case 'grade_multipla':
                case 'grade_caixas':
                    $linhas = array_keys($config['linhas'] ?? []);
                    $rules[$key] = [$required, 'array:'.implode(',', $linhas)];
                    foreach ($linhas as $linha) {
                        if ($pergunta->tipo === 'grade_multipla') {
                            $rules[$key.'.'.$linha] = [$required, 'integer', Rule::in($opcoes)];
                        } else {
                            $rules[$key.'.'.$linha] = [$required, 'array', 'min:1', 'max:'.count($opcoes)];
                            $rules[$key.'.'.$linha.'.*'] = ['required', 'integer', 'distinct', Rule::in($opcoes)];
                        }
                    }
                    break;
            }
        }

        return $rules;
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $versao = $this->input('versao');
            if (! is_string($versao) || ! hash_equals($this->formulario()->versaoRespostas(), $versao)) {
                $validator->errors()->add('formulario', 'O formulário foi alterado. Revise as perguntas antes de enviar novamente.');
            }
        }];
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach ($this->formulario()->perguntas as $pergunta) {
            $attributes['respostas.'.$pergunta->id] = $pergunta->titulo;
            foreach ($pergunta->configuracao['linhas'] ?? [] as $index => $linha) {
                $attributes['respostas.'.$pergunta->id.'.'.$index] = $pergunta->titulo.' — '.$linha;
            }
        }

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'identificador.required' => 'Informe seu CPF ou e-mail para enviar a resposta.',
            'required' => 'Preencha o campo :attribute.',
            'string' => 'Informe um texto válido em :attribute.',
            'max.string' => 'O campo :attribute deve ter até :max caracteres.',
            'max.array' => 'Selecione no máximo :max opções em :attribute.',
            'min.array' => 'Selecione pelo menos uma opção em :attribute.',
            'array' => 'Selecione respostas válidas em :attribute.',
            'integer' => 'Selecione uma opção válida em :attribute.',
            'in' => 'Selecione uma opção válida em :attribute.',
            'between' => 'A resposta de :attribute deve estar entre :min e :max.',
            'distinct' => 'Não repita opções em :attribute.',
            'date_format' => 'Informe uma data ou horário válido em :attribute.',
            'chave_envio.in' => 'Esta página expirou. Recarregue o formulário antes de enviar.',
        ];
    }
}
