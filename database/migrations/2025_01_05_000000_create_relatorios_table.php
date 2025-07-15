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
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->nullable()->constrained('users');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->text('conteudo');
            $table->enum('status', ['Rascunho', 'Pendente', 'Em Andamento', 'Concluído'])->default('Rascunho');
            $table->enum('prioridade', ['Baixa', 'Média', 'Alta'])->default('Média');
            $table->string('categoria')->nullable();
            $table->json('dados_adicionais')->nullable();
            $table->timestamp('data_vencimento')->nullable();
            $table->timestamp('data_conclusao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
