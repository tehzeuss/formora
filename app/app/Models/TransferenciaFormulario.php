<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferenciaFormulario extends Model
{
    public $timestamps = false;

    protected $table = 'transferencias_formularios';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['transferido_em' => 'datetime'];
    }

    public function administrador()
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }

    public function proprietarioAnterior()
    {
        return $this->belongsTo(User::class, 'proprietario_anterior_id');
    }

    public function proprietarioNovo()
    {
        return $this->belongsTo(User::class, 'proprietario_novo_id');
    }
}
