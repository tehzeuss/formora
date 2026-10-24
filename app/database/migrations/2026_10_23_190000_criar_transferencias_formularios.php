<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferencias_formularios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulario_id')->constrained('formularios')->cascadeOnDelete();
            $table->foreignId('administrador_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('proprietario_anterior_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('proprietario_novo_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('transferido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferencias_formularios');
    }
};
