<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setor;
use App\Models\User;
use App\Models\Equipamento;
use App\Models\Relatorio;
use App\Models\Local;

class TestRelatorioFiltersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar setores de teste
        $setor1 = Setor::create([
            'nome' => 'Produção',
            'descricao' => 'Setor de Produção',
            'ativo' => true,
        ]);

        $setor2 = Setor::create([
            'nome' => 'Manutenção',
            'descricao' => 'Setor de Manutenção',
            'ativo' => true,
        ]);

        // Criar usuários de teste
        $user1 = User::create([
            'name' => 'João Silva',
            'email' => 'joao.teste.' . time() . '@teste.com',
            'password' => bcrypt('password'),
            'setor' => 'Produção',
            'cargo' => 'Técnico',
            'role' => 'user',
            'ativo' => true,
        ]);

        $user2 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.teste.' . time() . '@teste.com',
            'password' => bcrypt('password'),
            'setor' => 'Manutenção',
            'cargo' => 'Engenheira',
            'role' => 'user',
            'ativo' => true,
        ]);

        // Criar local de teste
        $local = Local::create([
            'nome' => 'Galpão Principal',
            'setor' => 'Produção',
            'responsavel' => 'João Silva',
            'ativo' => true,
        ]);

        // Criar equipamentos de teste
        $equip1 = \App\Models\EquipamentoTest::create([
            'tag' => 'EQTEST001' . time(),
            'nome' => 'Motor Principal',
            'setor' => 'Produção',
            'status' => 'Operacional',
        ]);

        $equip2 = \App\Models\EquipamentoTest::create([
            'tag' => 'EQTEST002' . time(),
            'nome' => 'Compressor',
            'setor' => 'Manutenção',
            'status' => 'Operacional',
        ]);

        // Criar relatórios de teste
        $relatorio1 = Relatorio::create([
            'titulo' => 'Manutenção Preventiva Motor Principal',
            'setor_id' => $setor1->id,
            'activity' => 'Manutenção preventiva',
            'user_id' => $user1->id,
            'autor_id' => $user1->id,
            'nome_responsavel' => 'João Silva',
            'cargo_responsavel' => 'Técnico',
            'date_created' => now(),
            'time_created' => now(),
            'status' => 'Concluída',
            'progresso' => 100,
            'detalhes' => 'Manutenção preventiva realizada com sucesso',
        ]);

        $relatorio2 = Relatorio::create([
            'titulo' => 'Inspeção Compressor',
            'setor_id' => $setor2->id,
            'activity' => 'Inspeção técnica',
            'user_id' => $user2->id,
            'autor_id' => $user2->id,
            'nome_responsavel' => 'Maria Santos',
            'cargo_responsavel' => 'Engenheira',
            'date_created' => now(),
            'time_created' => now(),
            'status' => 'Em Andamento',
            'progresso' => 50,
            'detalhes' => 'Inspeção em andamento',
        ]);

        // Associar equipamentos de teste aos relatórios
        $relatorio1->equipamentosTeste()->attach($equip1->id);
        $relatorio2->equipamentosTeste()->attach($equip2->id);

        $this->command->info('Dados de teste criados com sucesso!');
        $this->command->info("Setores criados: {$setor1->nome}, {$setor2->nome}");
        $this->command->info("Usuários criados: {$user1->name}, {$user2->name}");
        $this->command->info("Equipamentos criados: {$equip1->equipment_tag}, {$equip2->equipment_tag}");
        $this->command->info("Relatórios criados: {$relatorio1->titulo}, {$relatorio2->titulo}");
    }
} 