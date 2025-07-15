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
        Schema::create('relatorio_atualizacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('progresso_anterior');
            $table->integer('progresso_novo');
            $table->string('status_anterior');
            $table->string('status_novo');
            $table->text('descricao');
            $table->json('imagens')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio_atualizacoes');
    }
};
