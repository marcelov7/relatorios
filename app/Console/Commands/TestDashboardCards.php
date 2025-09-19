<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Carbon\Carbon;

class TestDashboardCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard-cards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa as funcionalidades dos cards do dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando funcionalidades dos cards do dashboard...');

        // 1. Estatísticas gerais
        $this->info("\n📊 Estatísticas Gerais:");
        $stats = [
            'totalRelatorios' => Relatorio::count(),
            'esteMes' => Relatorio::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
            'emAndamento' => Relatorio::where('status', 'Em Andamento')->count(),
            'concluidos' => Relatorio::where('status', 'Concluída')->count(),
            'abertos' => Relatorio::where('status', 'Aberta')->count(),
        ];

        foreach ($stats as $key => $value) {
            $this->info("  - {$key}: {$value}");
        }

        // 2. Relatórios abertos que precisam de atenção
        $this->info("\n🔍 Relatórios Abertos (Aguardando Atenção):");
        $relatoriosAbertos = Relatorio::with(['autor', 'equipamentosTeste'])
            ->where('status', 'Aberta')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        if ($relatoriosAbertos->count() > 0) {
            foreach ($relatoriosAbertos as $relatorio) {
                $dataAtual = now()->startOfDay();
                $dataCriacao = $relatorio->created_at->startOfDay();
                $diasAberto = $dataAtual->diffInDays($dataCriacao);
                $prioridade = $diasAberto > 7 ? 'alta' : ($diasAberto > 3 ? 'media' : 'baixa');
                $setor = $relatorio->equipamentosTeste->first()->setor ?? 'Sem setor';
                
                // Formatar tempo de forma mais amigável
                if ($diasAberto == 0) {
                    $tempoAberto = 'Hoje';
                } elseif ($diasAberto == 1) {
                    $tempoAberto = 'Ontem';
                } elseif ($diasAberto < 7) {
                    $tempoAberto = "Há {$diasAberto} dias";
                } else {
                    $semanas = floor($diasAberto / 7);
                    $diasRestantes = $diasAberto % 7;
                    if ($diasRestantes == 0) {
                        $tempoAberto = $semanas == 1 ? "Há 1 semana" : "Há {$semanas} semanas";
                    } else {
                        $tempoAberto = "Há {$semanas} semana" . ($semanas > 1 ? 's' : '') . " e {$diasRestantes} dia" . ($diasRestantes > 1 ? 's' : '');
                    }
                }
                
                $this->info("  📋 {$relatorio->titulo}");
                $this->info("     Autor: {$relatorio->autor->name}");
                $this->info("     Setor: {$setor}");
                $this->info("     Dias aberto: {$diasAberto} ({$prioridade} prioridade)");
                $this->info("     Tempo: {$tempoAberto}");
                $this->info("     Criado em: {$relatorio->created_at->format('d/m/Y')}");
                $this->info("");
            }
        } else {
            $this->info("  ✅ Nenhum relatório aberto encontrado!");
        }

        // 3. Relatórios em andamento que podem precisar de atenção
        $this->info("\n⏳ Relatórios em Andamento (Precisam de Atenção):");
        $relatoriosEmAndamento = Relatorio::with(['autor', 'equipamentosTeste'])
            ->where('status', 'Em Andamento')
            ->where('progresso', '<', 100)
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        if ($relatoriosEmAndamento->count() > 0) {
            foreach ($relatoriosEmAndamento as $relatorio) {
                $dataAtual = now()->startOfDay();
                $dataUltimaAtualizacao = $relatorio->updated_at->startOfDay();
                $diasSemAtualizacao = $dataAtual->diffInDays($dataUltimaAtualizacao);
                $precisaAtencao = $diasSemAtualizacao > 3;
                $setor = $relatorio->equipamentosTeste->first()->setor ?? 'Sem setor';
                
                // Formatar tempo sem atualização de forma mais amigável
                if ($diasSemAtualizacao == 0) {
                    $tempoSemAtualizacao = 'Atualizado hoje';
                } elseif ($diasSemAtualizacao == 1) {
                    $tempoSemAtualizacao = 'Atualizado ontem';
                } elseif ($diasSemAtualizacao < 7) {
                    $tempoSemAtualizacao = "{$diasSemAtualizacao} dias sem atualização";
                } else {
                    $semanas = floor($diasSemAtualizacao / 7);
                    $diasRestantes = $diasSemAtualizacao % 7;
                    if ($diasRestantes == 0) {
                        $tempoSemAtualizacao = $semanas == 1 ? "1 semana sem atualização" : "{$semanas} semanas sem atualização";
                    } else {
                        $tempoSemAtualizacao = "{$semanas} semana" . ($semanas > 1 ? 's' : '') . " e {$diasRestantes} dia" . ($diasRestantes > 1 ? 's' : '') . " sem atualização";
                    }
                }
                
                $this->info("  📋 {$relatorio->titulo}");
                $this->info("     Autor: {$relatorio->autor->name}");
                $this->info("     Setor: {$setor}");
                $this->info("     Progresso: {$relatorio->progresso}%");
                $this->info("     Dias sem atualização: {$diasSemAtualizacao}");
                $this->info("     Tempo: {$tempoSemAtualizacao}");
                $this->info("     Precisa atenção: " . ($precisaAtencao ? 'SIM' : 'NÃO'));
                $this->info("     Última atualização: {$relatorio->updated_at->format('d/m/Y')}");
                $this->info("");
            }
        } else {
            $this->info("  ✅ Nenhum relatório em andamento encontrado!");
        }

        // 4. Análise de prioridades
        $this->info("\n🎯 Análise de Prioridades:");
        
        $altaPrioridade = $relatoriosAbertos->filter(function($r) {
            return now()->diffInDays($r->created_at) > 7;
        })->count();
        
        $mediaPrioridade = $relatoriosAbertos->filter(function($r) {
            $dias = now()->diffInDays($r->created_at);
            return $dias > 3 && $dias <= 7;
        })->count();
        
        $baixaPrioridade = $relatoriosAbertos->filter(function($r) {
            return now()->diffInDays($r->created_at) <= 3;
        })->count();

        $this->info("  🔴 Alta prioridade (>7 dias): {$altaPrioridade}");
        $this->info("  🟡 Média prioridade (3-7 dias): {$mediaPrioridade}");
        $this->info("  🟢 Baixa prioridade (≤3 dias): {$baixaPrioridade}");

        // 5. Relatórios que precisam de atenção (em andamento)
        $precisamAtencao = $relatoriosEmAndamento->filter(function($r) {
            return now()->diffInDays($r->updated_at) > 3;
        })->count();

        $this->info("  ⚠️  Em andamento precisam atenção (>3 dias sem atualização): {$precisamAtencao}");

        // 6. Sugestões de ação
        $this->info("\n💡 Sugestões de Ação:");
        
        if ($altaPrioridade > 0) {
            $this->warn("  🔴 ATENÇÃO: {$altaPrioridade} relatório(s) com alta prioridade!");
            $this->info("     Ação recomendada: Revisar e iniciar trabalho imediatamente");
        }
        
        if ($precisamAtencao > 0) {
            $this->warn("  ⚠️  ATENÇÃO: {$precisamAtencao} relatório(s) em andamento sem atualização!");
            $this->info("     Ação recomendada: Verificar status e atualizar progresso");
        }
        
        if ($altaPrioridade === 0 && $precisamAtencao === 0) {
            $this->info("  ✅ Todos os relatórios estão em dia!");
        }

        $this->info("\n🎉 Teste dos cards do dashboard concluído!");
    }
} 