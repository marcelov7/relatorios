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
        Schema::create('relatorio_imagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->string('nome_original');
            $table->string('nome_arquivo');
            $table->string('caminho_original');
            $table->string('caminho_thumb')->nullable();
            $table->string('caminho_medium')->nullable();
            $table->string('mime_type');
            $table->bigInteger('tamanho');
            $table->integer('ordem')->default(0);
            $table->timestamps();

            // Índices para performance
            $table->index(['relatorio_id', 'ordem']);
            $table->index('caminho_original');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio_imagens');
    }
}; 