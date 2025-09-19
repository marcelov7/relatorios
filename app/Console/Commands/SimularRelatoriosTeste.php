<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use App\Models\User;
use App\Models\EquipamentoTest;
use Carbon\Carbon;

class SimularRelatoriosTeste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simular:relatorios-teste';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula relatórios com diferentes datas para testar os cards do dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎭 Simulando relatórios para teste dos cards...');

        // Buscar usuário e equipamento de teste
        $usuario = User::first();
        $equipamentoTeste = EquipamentoTest::first();

        if (!$usuario || !$equipamentoTeste) {
            $this->error('❌ Usuário ou equipamento de teste não encontrado!');
            return;
        }

        // Limpar relatórios existentes (apenas para teste)
        $this->warn('⚠️  Limpando relatórios existentes...');
        Relatorio::truncate();

        // 1. Relatório aberto há 10 dias (alta prioridade)
        $this->info('📋 Criando relatório aberto há 10 dias (alta prioridade)...');
        Relatorio::create([
            'titulo' => 'Manutenção Preventiva - Motor Principal',
            'activity' => 'Inspeção e lubrificação do motor principal',
            'nome_responsavel' => 'João Silva',
            'cargo_responsavel' => 'Técnico de Manutenção',
            'status' => 'Aberta',
            'progresso' => 0,
            'detalhes' => 'Relatório de manutenção preventiva do motor principal da linha de produção.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        // 2. Relatório aberto há 5 dias (média prioridade)
        $this->info('📋 Criando relatório aberto há 5 dias (média prioridade)...');
        Relatorio::create([
            'titulo' => 'Limpeza Sistema de Filtros',
            'activity' => 'Limpeza e troca de filtros do sistema de ar',
            'nome_responsavel' => 'Maria Santos',
            'cargo_responsavel' => 'Técnica de Qualidade',
            'status' => 'Aberta',
            'progresso' => 0,
            'detalhes' => 'Limpeza e manutenção do sistema de filtros de ar comprimido.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        // 3. Relatório aberto há 1 dia (baixa prioridade)
        $this->info('📋 Criando relatório aberto há 1 dia (baixa prioridade)...');
        Relatorio::create([
            'titulo' => 'Verificação de Segurança - Setor A',
            'activity' => 'Verificação dos equipamentos de segurança',
            'nome_responsavel' => 'Pedro Costa',
            'cargo_responsavel' => 'Técnico de Segurança',
            'status' => 'Aberta',
            'progresso' => 0,
            'detalhes' => 'Verificação semanal dos equipamentos de segurança do setor A.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        // 4. Relatório em andamento há 8 dias sem atualização (precisa atenção)
        $this->info('📋 Criando relatório em andamento há 8 dias sem atualização...');
        Relatorio::create([
            'titulo' => 'Reparo Compressor Industrial',
            'activity' => 'Reparo e manutenção do compressor principal',
            'nome_responsavel' => 'Carlos Oliveira',
            'cargo_responsavel' => 'Mecânico Industrial',
            'status' => 'Em Andamento',
            'progresso' => 60,
            'detalhes' => 'Reparo do compressor industrial que apresentou falhas.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(8),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        // 5. Relatório em andamento há 2 dias sem atualização (normal)
        $this->info('📋 Criando relatório em andamento há 2 dias sem atualização...');
        Relatorio::create([
            'titulo' => 'Instalação Novo Sistema de Controle',
            'activity' => 'Instalação e configuração do novo sistema',
            'nome_responsavel' => 'Ana Paula',
            'cargo_responsavel' => 'Técnica de Automação',
            'status' => 'Em Andamento',
            'progresso' => 80,
            'detalhes' => 'Instalação e configuração do novo sistema de controle industrial.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(2),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        // 6. Relatório concluído recentemente
        $this->info('📋 Criando relatório concluído recentemente...');
        Relatorio::create([
            'titulo' => 'Calibração Instrumentos de Medição',
            'activity' => 'Calibração dos instrumentos de medição',
            'nome_responsavel' => 'Roberto Lima',
            'cargo_responsavel' => 'Técnico de Metrologia',
            'status' => 'Concluída',
            'progresso' => 100,
            'detalhes' => 'Calibração de todos os instrumentos de medição do laboratório.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDay(),
        ])->equipamentosTeste()->attach($equipamentoTeste->id);

        $this->info('✅ Relatórios de teste criados com sucesso!');
        $this->info('');
        $this->info('📊 Resumo dos relatórios criados:');
        $this->info('  🔴 Alta prioridade (10 dias): 1');
        $this->info('  🟡 Média prioridade (5 dias): 1');
        $this->info('  🟢 Baixa prioridade (1 dia): 1');
        $this->info('  ⏳ Em andamento (8 dias sem atualização): 1');
        $this->info('  ⏳ Em andamento (2 dias sem atualização): 1');
        $this->info('  ✅ Concluído: 1');
        $this->info('');
        $this->info('🧪 Agora você pode testar os cards do dashboard!');
        $this->info('   Execute: php artisan test:dashboard-cards');
    }
} 