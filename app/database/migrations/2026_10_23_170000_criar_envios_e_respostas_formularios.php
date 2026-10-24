<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios_formularios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulario_id')->constrained('formularios')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('identificado');
            $table->uuid('chave_envio')->unique();
            $table->string('titulo_formulario');
            $table->timestamp('enviado_em');
        });
        Schema::create('respostas_formularios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_id')->constrained('envios_formularios')->cascadeOnDelete();
            $table->foreignId('pergunta_id')->nullable()->constrained('perguntas_formulario')->nullOnDelete();
            $table->string('titulo_pergunta');
            $table->string('tipo_pergunta', 30);
            $table->boolean('obrigatoria');
            $table->unsignedInteger('ordem');
            $table->json('opcoes')->nullable();
            $table->json('configuracao')->nullable();
            $table->json('valor')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas_formularios');
        Schema::dropIfExists('envios_formularios');
    }
};
