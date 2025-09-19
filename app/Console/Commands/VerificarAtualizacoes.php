<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use App\Models\RelatorioAtualizacao;

class VerificarAtualizacoes extends Command
{
    protected $signature = 'verificar:atualizacoes';
    protected $description = 'Verifica os dados das atualizações de relatórios';

    public function handle()
    {
        $this->info('🔍 Verificando dados das atualizações de relatórios...');
        
        // Verificar relatórios com atualizações
        $relatorios = Relatorio::with(['atualizacoes.usuario'])
            ->whereHas('atualizacoes')
            ->take(5)
            ->get();
        
        $this->info("📊 Relatórios com atualizações encontrados: " . $relatorios->count());
        
        if ($relatorios->isEmpty()) {
            $this->warn('⚠️  Nenhum relatório com atualizações encontrado!');
            return;
        }
        
        foreach ($relatorios as $relatorio) {
            $this->info("\n📋 Relatório: {$relatorio->titulo} (ID: {$relatorio->id})");
            $this->info("   Status atual: {$relatorio->status}");
            $this->info("   Progresso atual: {$relatorio->progresso}%");
            $this->info("   Atualizações: " . $relatorio->atualizacoes->count());
            
            foreach ($relatorio->atualizacoes as $atualizacao) {
                $this->info("   └─ Atualização ID: {$atualizacao->id}");
                $this->info("      Data: " . $atualizacao->created_at->format('d/m/Y H:i'));
                $this->info("      Usuário: " . ($atualizacao->usuario->name ?? 'N/A'));
                $this->info("      Progresso: {$atualizacao->progresso_anterior}% → {$atualizacao->progresso_novo}%");
                $this->info("      Status: {$atualizacao->status_anterior} → {$atualizacao->status_novo}");
                
                if ($atualizacao->descricao) {
                    $this->info("      Descrição: {$atualizacao->descricao}");
                }
                
                if (is_array($atualizacao->imagens) && count($atualizacao->imagens) > 0) {
                    $this->info("      Imagens: " . count($atualizacao->imagens));
                }
            }
        }
        
        // Verificar se há dados nulos ou vazios
        $this->info("\n🔍 Verificando dados problemáticos...");
        
        $atualizacoesComProblemas = RelatorioAtualizacao::where(function($query) {
            $query->whereNull('progresso_anterior')
                  ->orWhereNull('progresso_novo')
                  ->orWhereNull('status_anterior')
                  ->orWhereNull('status_novo');
        })->get();
        
        if ($atualizacoesComProblemas->count() > 0) {
            $this->warn("⚠️  Encontradas {$atualizacoesComProblemas->count()} atualizações com dados nulos:");
            
            foreach ($atualizacoesComProblemas as $atualizacao) {
                $this->info("   └─ ID: {$atualizacao->id}");
                $this->info("      Progresso anterior: " . ($atualizacao->progresso_anterior ?? 'NULL'));
                $this->info("      Progresso novo: " . ($atualizacao->progresso_novo ?? 'NULL'));
                $this->info("      Status anterior: " . ($atualizacao->status_anterior ?? 'NULL'));
                $this->info("      Status novo: " . ($atualizacao->status_novo ?? 'NULL'));
            }
        } else {
            $this->info("✅ Nenhuma atualização com dados nulos encontrada!");
        }
        
        // Verificar valores específicos
        $this->info("\n🔍 Verificando valores específicos...");
        
        $atualizacoesComValoresEstranhos = RelatorioAtualizacao::where(function($query) {
            $query->where('progresso_anterior', '?')
                  ->orWhere('progresso_novo', '?')
                  ->orWhere('status_anterior', '?')
                  ->orWhere('status_novo', '?');
        })->get();
        
        if ($atualizacoesComValoresEstranhos->count() > 0) {
            $this->error("❌ Encontradas {$atualizacoesComValoresEstranhos->count()} atualizações com valores '?':");
            
            foreach ($atualizacoesComValoresEstranhos as $atualizacao) {
                $this->info("   └─ ID: {$atualizacao->id}");
                $this->info("      Progresso anterior: '{$atualizacao->progresso_anterior}'");
                $this->info("      Progresso novo: '{$atualizacao->progresso_novo}'");
                $this->info("      Status anterior: '{$atualizacao->status_anterior}'");
                $this->info("      Status novo: '{$atualizacao->status_novo}'");
            }
        } else {
            $this->info("✅ Nenhuma atualização com valores '?' encontrada!");
        }
    }
} 