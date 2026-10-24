<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formularios', function (Blueprint $table) {
            $table->string('situacao', 20)->default('rascunho');
            $table->boolean('exige_identificacao')->default(false);
            $table->string('token_compartilhamento', 64)->nullable()->unique();
            $table->timestamp('publicado_em')->nullable();
            $table->timestamp('encerrado_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('formularios', function (Blueprint $table) {
            $table->dropUnique(['token_compartilhamento']);
            $table->dropColumn(['situacao', 'exige_identificacao', 'token_compartilhamento', 'publicado_em', 'encerrado_em']);
        });
    }
};
