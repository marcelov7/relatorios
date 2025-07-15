<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->string('nome_responsavel')->nullable()->after('autor_id');
            $table->string('cargo_responsavel')->nullable()->after('nome_responsavel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->dropColumn(['nome_responsavel', 'cargo_responsavel']);
        });
    }
};
