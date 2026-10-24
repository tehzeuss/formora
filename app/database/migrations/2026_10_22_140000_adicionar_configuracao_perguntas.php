<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perguntas_formulario', function (Blueprint $table) {
            $table->json('configuracao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('perguntas_formulario', function (Blueprint $table) {
            $table->dropColumn('configuracao');
        });
    }
};
