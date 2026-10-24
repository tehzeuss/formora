<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public static function resumoAdministrativo(): array
    {
        $grupos = static::selectRaw('ativo, perfil_id, count(*) as quantidade')->groupBy('ativo', 'perfil_id')->get();

        return [
            'total' => (int) $grupos->sum('quantidade'),
            'ativos' => (int) $grupos->where('ativo', true)->sum('quantidade'),
            'inativos' => (int) $grupos->where('ativo', false)->sum('quantidade'),
            'administradores' => (int) $grupos->where('perfil_id', Perfil::ADMINISTRADOR)->sum('quantidade'),
        ];
    }

    public static function cadastrarConta(array $dados): self
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($dados) {
            Perfil::whereKey(Perfil::ADMINISTRADOR)->lockForUpdate()->firstOrFail();
            if (self::where('email', $dados['email'])->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'Este e-mail já está cadastrado.']);
            }
            $usuario = new self;
            $usuario->fill(\Illuminate\Support\Arr::only($dados, ['name', 'email']));
            $usuario->password = $dados['password'];
            $usuario->perfil_id = $dados['perfil_id'];
            $usuario->ativo = $dados['ativo'];
            $usuario->save();

            return $usuario;
        });
    }

    public function atualizarCadastroAdministrativo(array $dados): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($dados) {
            Perfil::whereKey(Perfil::ADMINISTRADOR)->lockForUpdate()->firstOrFail();
            $this->refresh();
            if ($this->eAdministrador() && $this->ativo
                && (! $dados['ativo'] || (int) $dados['perfil_id'] !== Perfil::ADMINISTRADOR)
                && ! self::where('perfil_id', Perfil::ADMINISTRADOR)->where('ativo', true)->whereKeyNot($this->id)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['perfil_id' => 'Mantenha pelo menos um administrador ativo no sistema.']);
            }
            $this->fill(\Illuminate\Support\Arr::only($dados, ['name', 'email']));
            if (! empty($dados['password'])) {
                $this->password = $dados['password'];
                $this->remember_token = \Illuminate\Support\Str::random(60);
            }
            $this->perfil_id = $dados['perfil_id'];
            $this->ativo = $dados['ativo'];
            $this->save();
        });
    }

    protected $fillable = ['name', 'email'];

    protected $hidden = ['password', 'remember_token'];

    use Notifiable;

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function eAdministrador(): bool
    {
        return $this->perfil_id === Perfil::ADMINISTRADOR;
    }

    public function eUsuario(): bool
    {
        return $this->perfil_id === Perfil::USUARIO;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'perfil_id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
