<?php

namespace App\Models\Concerns;

use App\Support\TextoFormatado;

trait TemTituloFormatado
{
    public function tituloFormatadoSeguro(): string
    {
        return $this->titulo_formatado ? TextoFormatado::limpar($this->titulo_formatado) : e($this->titulo);
    }
}
