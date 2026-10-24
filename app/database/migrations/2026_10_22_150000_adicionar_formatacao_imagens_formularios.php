<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['formularios', 'perguntas_formulario'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->text('titulo_formatado')->nullable();
                $table->string('imagem')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['perguntas_formulario', 'formularios'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropColumn(['titulo_formatado', 'imagem']);
            });
        }
    }
};
