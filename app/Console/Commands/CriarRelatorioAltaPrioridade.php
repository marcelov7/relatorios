<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use App\Models\User;
use App\Models\EquipamentoTest;
use Carbon\Carbon;

class CriarRelatorioAltaPrioridade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'criar:alta-prioridade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um relatório com alta prioridade para testar o card';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔴 Criando relatório com alta prioridade...');

        // Buscar usuário e equipamento de teste
        $usuario = User::first();
        $equipamentoTeste = EquipamentoTest::first();

        if (!$usuario || !$equipamentoTeste) {
            $this->error('❌ Usuário ou equipamento de teste não encontrado!');
            return;
        }

        // Criar relatório há 10 dias (alta prioridade)
        $relatorio = Relatorio::create([
            'titulo' => 'Manutenção Urgente - Sistema de Refrigeração',
            'activity' => 'Reparo emergencial do sistema de refrigeração',
            'nome_responsavel' => 'João Silva',
            'cargo_responsavel' => 'Técnico de Manutenção',
            'status' => 'Aberta',
            'progresso' => 0,
            'detalhes' => 'Sistema de refrigeração apresentando falhas críticas. Necessita intervenção imediata.',
            'autor_id' => $usuario->id,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $relatorio->equipamentosTeste()->attach($equipamentoTeste->id);

        $this->info('✅ Relatório com alta prioridade criado!');
        $this->info('');
        $this->info('📋 Detalhes do relatório:');
        $this->info("  Título: {$relatorio->titulo}");
        $this->info("  Status: {$relatorio->status}");
        $this->info("  Criado há: 10 dias");
        $this->info("  Prioridade: Alta (>7 dias)");
        $this->info('');
        $this->info('🧪 Agora teste o card do dashboard!');
        $this->info('   Execute: php artisan test:alta-prioridade');
    }
} 