<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamp('criado_em')->nullable();
            $table->timestamp('atualizado_em')->nullable();
        });

        $agora = now();
        DB::table('perfis')->insert([
            ['id' => 1, 'nome' => 'Usuário', 'criado_em' => $agora, 'atualizado_em' => $agora],
            ['id' => 2, 'nome' => 'Administrador', 'criado_em' => $agora, 'atualizado_em' => $agora],
        ]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('perfis', 'id'), (SELECT MAX(id) FROM perfis))");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('perfil_id')->default(1)->constrained('perfis')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perfil_id']);
            $table->dropColumn('perfil_id');
        });

        Schema::dropIfExists('perfis');
    }
};
