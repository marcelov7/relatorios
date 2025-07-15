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
        Schema::create('relatorio_equipamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->foreignId('equipamento_id')->constrained('equipamentos')->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicatas
            $table->unique(['relatorio_id', 'equipamento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio_equipamento');
    }
};
