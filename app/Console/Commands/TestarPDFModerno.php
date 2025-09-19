<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarPDFModerno extends Command
{
    protected $signature = 'testar:pdf-moderno';
    protected $description = 'Testa a geração do PDF com estilo moderno';

    public function handle()
    {
        $this->info('🧪 Testando geração do PDF Moderno...');
        
        // Buscar relatórios
        $relatorios = Relatorio::with(['autor', 'equipamentosTeste', 'atualizacoes.usuario'])
            ->take(3)
            ->get();
        
        $this->info("📊 Relatórios encontrados: " . $relatorios->count());
        
        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }
        
        // Calcular totais para cada relatório
        foreach ($relatorios as $relatorio) {
            $relatorio->totalFotos = is_array($relatorio->images) ? count($relatorio->images) : 0;
            $relatorio->totalHistoricos = $relatorio->atualizacoes ? $relatorio->atualizacoes->count() : 0;
        }
        
        // Dados para o PDF
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => 'Sistema de Relatórios InterCement'
        ];
        
        try {
            // Gerar PDF com estilo moderno
            $pdf = Pdf::loadView('relatorios.pdf-moderno', $data);
            $pdf->setPaper('A4', 'portrait');
            
            // Salvar arquivo
            $caminho = storage_path('app/test-pdf-moderno.pdf');
            $pdf->save($caminho);
            
            $this->info('✅ PDF Moderno gerado com sucesso!');
            $this->info("📁 Arquivo salvo em: $caminho");
            $this->newLine();
            
            $this->info('🎯 Para visualizar:');
            $this->info("   1. Abra o arquivo: $caminho");
            $this->info('   2. Verifique se o estilo moderno está correto');
            $this->info('   3. Confirme se o layout está bem formatado');
            $this->newLine();
            
            $this->info('📋 Relatórios incluídos no teste:');
            foreach ($relatorios as $relatorio) {
                $this->info("   - {$relatorio->titulo} ({$relatorio->status})");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar PDF: ' . $e->getMessage());
        }
    }
} 