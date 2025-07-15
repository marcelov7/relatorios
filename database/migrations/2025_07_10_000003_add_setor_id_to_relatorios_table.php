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
            $table->foreignId('setor_id')->nullable()->after('user_id')->constrained('setores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->dropForeign(['setor_id']);
            $table->dropColumn('setor_id');
        });
    }
}; 