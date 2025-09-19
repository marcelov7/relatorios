<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Carbon\Carbon;

class TestRelatoriosAltaPrioridade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:alta-prioridade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa especificamente os relatórios com alta prioridade';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testando relatórios com alta prioridade...');

        // Buscar relatórios abertos
        $relatoriosAbertos = Relatorio::with(['autor', 'equipamentosTeste'])
            ->where('status', 'Aberta')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->info("\n📊 Total de relatórios abertos: " . $relatoriosAbertos->count());

        // Contar por prioridade
        $altaPrioridade = 0;
        $mediaPrioridade = 0;
        $baixaPrioridade = 0;

        foreach ($relatoriosAbertos as $relatorio) {
            $dataAtual = now()->startOfDay();
            $dataCriacao = $relatorio->created_at->startOfDay();
            $diasAberto = $dataAtual->diffInDays($dataCriacao);
            
            if ($diasAberto > 7) {
                $prioridade = 'alta';
                $altaPrioridade++;
            } elseif ($diasAberto > 3) {
                $prioridade = 'media';
                $mediaPrioridade++;
            } else {
                $prioridade = 'baixa';
                $baixaPrioridade++;
            }

            $this->info("  📋 {$relatorio->titulo}");
            $this->info("     Dias aberto: {$diasAberto}");
            $this->info("     Prioridade: {$prioridade}");
            $this->info("");
        }

        $this->info("🎯 Resumo de Prioridades:");
        $this->info("  🔴 Alta prioridade (>7 dias): {$altaPrioridade}");
        $this->info("  🟡 Média prioridade (3-7 dias): {$mediaPrioridade}");
        $this->info("  🟢 Baixa prioridade (≤3 dias): {$baixaPrioridade}");

        // Testar o filtro Vue.js
        $this->info("\n🧪 Testando filtro Vue.js:");
        $altaPrioridadeFiltro = $relatoriosAbertos->filter(function($r) {
            $dataAtual = now()->startOfDay();
            $dataCriacao = $r->created_at->startOfDay();
            $diasAberto = $dataAtual->diffInDays($dataCriacao);
            return $diasAberto > 7;
        })->count();

        $this->info("  Relatórios com alta prioridade (filtro): {$altaPrioridadeFiltro}");

        if ($altaPrioridadeFiltro > 0) {
            $this->info("  ✅ O card deve mostrar: '{$altaPrioridadeFiltro} alta prioridade'");
        } else {
            $this->info("  ℹ️  O card não deve mostrar tag de alta prioridade");
        }

        $this->info("\n🎉 Teste concluído!");
    }
} 