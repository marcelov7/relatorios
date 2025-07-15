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
        Schema::dropIfExists('relatorio_atribuicoes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('relatorio_atribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('atribuido_por')->constrained('users')->onDelete('cascade');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Evitar atribuições duplicadas
            $table->unique(['relatorio_id', 'user_id']);
        });
    }
};
