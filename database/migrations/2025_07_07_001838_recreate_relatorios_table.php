<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backup dos dados existentes
        $relatoriosExistentes = DB::table('relatorios')->get();

        // Dropar a tabela existente
        Schema::dropIfExists('relatorios');

        // Recriar a tabela com a nova estrutura (incluindo autor_id)
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->nullable()->constrained('users');
            $table->string('titulo');
            $table->string('sector')->nullable();
            $table->string('activity')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date_created')->nullable();
            $table->foreignId('local_id')->nullable()->constrained('locais')->onDelete('set null');
            $table->foreignId('equipment_id')->nullable()->constrained('equipamentos')->onDelete('set null');
            $table->string('equipment_tag')->nullable();
            $table->text('description')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('detalhes');
            $table->enum('status', ['Aberta', 'Em Andamento', 'Concluída', 'Cancelada'])->default('Aberta');
            $table->integer('progresso')->default(0);
            $table->timestamps();
        });

        // Restaurar dados existentes com adaptação
        foreach ($relatoriosExistentes as $relatorio) {
            DB::table('relatorios')->insert([
                'id' => $relatorio->id,
                'autor_id' => null, // ajuste conforme necessário
                'titulo' => $relatorio->titulo,
                'sector' => 'Não Informado',
                'activity' => 'Atividade Geral',
                'user_id' => 1,
                'date_created' => now()->format('Y-m-d'),
                'local_id' => null,
                'equipment_id' => null,
                'equipment_tag' => null,
                'description' => null,
                'observacoes' => $relatorio->descricao ?? null,
                'detalhes' => $relatorio->conteudo,
                'status' => $this->mapOldStatus($relatorio->status),
                'progresso' => 0,
                'created_at' => $relatorio->created_at,
                'updated_at' => $relatorio->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // Backup dos dados atuais
        $relatoriosAtuais = DB::table('relatorios')->get();

        // Dropar a tabela atual
        Schema::dropIfExists('relatorios');

        // Recriar a tabela com a estrutura original (incluindo autor_id)
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

        // Restaurar dados na estrutura original
        foreach ($relatoriosAtuais as $relatorio) {
            DB::table('relatorios')->insert([
                'id' => $relatorio->id,
                'autor_id' => null, // ajuste conforme necessário
                'titulo' => $relatorio->titulo,
                'descricao' => $relatorio->observacoes,
                'conteudo' => $relatorio->detalhes,
                'status' => $this->mapNewStatus($relatorio->status),
                'prioridade' => 'Média',
                'categoria' => null,
                'dados_adicionais' => null,
                'data_vencimento' => null,
                'data_conclusao' => null,
                'created_at' => $relatorio->created_at,
                'updated_at' => $relatorio->updated_at,
            ]);
        }
    }

    private function mapOldStatus($oldStatus)
    {
        return match($oldStatus) {
            'Rascunho' => 'Aberta',
            'Pendente' => 'Aberta',
            'Em Andamento' => 'Em Andamento',
            'Concluído' => 'Concluída',
            default => 'Aberta'
        };
    }

    private function mapNewStatus($newStatus)
    {
        return match($newStatus) {
            'Aberta' => 'Pendente',
            'Em Andamento' => 'Em Andamento',
            'Concluída' => 'Concluído',
            'Cancelada' => 'Rascunho',
            default => 'Rascunho'
        };
    }
};