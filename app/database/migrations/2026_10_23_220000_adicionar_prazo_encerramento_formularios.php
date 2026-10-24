<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formularios', fn (Blueprint $table) => $table->timestamp('prazo_encerramento')->nullable());
    }

    public function down(): void
    {
        Schema::table('formularios', fn (Blueprint $table) => $table->dropColumn('prazo_encerramento'));
    }
};
