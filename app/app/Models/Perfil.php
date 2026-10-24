<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfil extends Model
{
    public const USUARIO = 1;

    public const ADMINISTRADOR = 2;

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $table = 'perfis';

    protected $fillable = ['nome'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'perfil_id');
    }
}
