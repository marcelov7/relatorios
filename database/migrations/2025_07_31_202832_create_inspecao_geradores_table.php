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
        Schema::create('inspecao_geradores', function (Blueprint $table) {
            $table->id();
            
            // Data e colaborador
            $table->datetime('data_inspecao');
            $table->foreignId('colaborador_id')->constrained('users')->onDelete('cascade');
            
            // Níveis (parado)
            $table->enum('nivel_oleo_motor_parado', ['MÍNIMO', 'NORMAL']);
            $table->enum('nivel_agua_parado', ['MÍNIMO', 'NORMAL']);
            
            // Sincronização
            $table->decimal('sync_gerador', 5, 2)->nullable();
            $table->decimal('sync_rede', 5, 2)->nullable();
            
            // Temperatura
            $table->decimal('temperatura_agua', 5, 2)->nullable();
            
            // Pressão e frequência
            $table->decimal('pressao_oleo', 5, 2)->nullable();
            $table->decimal('frequencia', 5, 2)->nullable();
            
            // Tensões
            $table->decimal('tensao_a', 6, 2)->nullable();
            $table->decimal('tensao_b', 6, 2)->nullable();
            $table->decimal('tensao_c', 6, 2)->nullable();
            
            // RPM e tensões do sistema
            $table->integer('rpm_1800')->nullable();
            $table->decimal('tensao_bateria_parado', 5, 2)->nullable();
            $table->decimal('tensao_alternador_marcha', 5, 2)->nullable();
            
            // Combustível
            $table->integer('nivel_combustivel')->nullable();
            
            // Condições da sala
            $table->boolean('iluminacao_sala_deficiente')->default(false);
            $table->boolean('limpeza_sala_realizada')->default(false);
            
            // Situação (calculada automaticamente)
            $table->enum('situacao', ['OK - PRONTO ( AUTO)', 'ANORMAL - PRECISA DE INSPEÇÃO'])->default('OK - PRONTO ( AUTO)');
            
            // Observações
            $table->text('observacoes')->nullable();
            
            // Relacionamentos
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('setor_id')->nullable()->constrained('setores')->onDelete('set null');
            
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index(['data_inspecao']);
            $table->index(['situacao']);
            $table->index(['colaborador_id']);
            $table->index(['setor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecao_geradores');
    }
};
