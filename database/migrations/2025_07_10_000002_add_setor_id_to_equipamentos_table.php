<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('setor_id')->nullable()->after('id');
            $table->foreign('setor_id')->references('id')->on('setores')->onDelete('restrict');
            // Se existir o campo setor antigo (string), remova:
            if (Schema::hasColumn('equipamentos', 'setor')) {
                $table->dropColumn('setor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->dropForeign(['setor_id']);
            $table->dropColumn('setor_id');
            // Opcional: recriar o campo setor antigo
            // $table->string('setor')->nullable();
        });
    }
}; 