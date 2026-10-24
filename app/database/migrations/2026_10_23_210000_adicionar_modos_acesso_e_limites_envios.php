<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formularios', function (Blueprint $table) {
            $table->string('modo_acesso', 20)->default('aberto_livre');
            $table->string('tipo_identificador', 10)->nullable();
        });
        DB::table('formularios')->where('exige_identificacao', true)->update(['modo_acesso' => 'fechado']);
        Schema::table('envios_formularios', function (Blueprint $table) {
            $table->string('tipo_identificador', 10)->nullable();
            $table->string('identificador', 255)->nullable();
            $table->string('chave_participante', 64)->nullable();
            $table->unique(['formulario_id', 'chave_participante'], 'envios_participante_unico');
        });
    }

    public function down(): void
    {
        Schema::table('envios_formularios', function (Blueprint $table) {
            $table->dropUnique('envios_participante_unico');
            $table->dropColumn(['tipo_identificador', 'identificador', 'chave_participante']);
        });
        Schema::table('formularios', fn (Blueprint $table) => $table->dropColumn(['modo_acesso', 'tipo_identificador']));
    }
};
