<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class IdentificadorResposta
{
    public static function normalizar(string $tipo, mixed $entrada): string
    {
        $valor = is_string($entrada) ? trim($entrada) : '';
        if ($tipo === 'email') {
            $valor = mb_strtolower($valor);
            if (strlen($valor) <= 254 && filter_var($valor, FILTER_VALIDATE_EMAIL)) return $valor;
            throw ValidationException::withMessages(['identificador' => 'Informe um e-mail válido.']);
        }
        if ($tipo === 'cpf') {
            $valor = preg_replace('/[.\-\s]/u', '', $valor);
            if (preg_match('/^[0-9]{11}$/', $valor) && ! preg_match('/^(\d)\1{10}$/', $valor)) {
                $valido = true;
                for ($tamanho = 9; $tamanho <= 10; $tamanho++) {
                    $soma = 0;
                    for ($indice = 0; $indice < $tamanho; $indice++) {
                        $soma += (int) $valor[$indice] * ($tamanho + 1 - $indice);
                    }
                    $digito = (10 * $soma) % 11;
                    if (($digito === 10 ? 0 : $digito) !== (int) $valor[$tamanho]) $valido = false;
                }
                if ($valido) return $valor;
            }
            throw ValidationException::withMessages(['identificador' => 'Informe um CPF válido.']);
        }
        throw ValidationException::withMessages(['identificador' => 'Tipo de identificação inválido. Recarregue a página.']);
    }

    public static function chave(string $tipo, string $valor): string
    {
        return hash('sha256', $tipo.':'.$valor);
    }
}
