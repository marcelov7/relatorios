<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\Snappy\Facades\SnappyPdf;

class TestarPDFComMelhorias extends Command
{
    protected $signature = 'testar:pdf-melhorias {relatorio_id?}';
    protected $description = 'Testa a geração do PDF com melhorias nas atualizações';

    public function handle()
    {
        $relatorioId = $this->argument('relatorio_id');
        
        if ($relatorioId) {
            $relatorio = Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->find($relatorioId);
        } else {
            $relatorio = Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->whereHas('atualizacoes')
                ->first();
        }
        
        if (!$relatorio) {
            $this->error('❌ Nenhum relatório com atualizações encontrado!');
            return;
        }
        
        $this->info("📋 Testando PDF para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        $this->info("   Status: {$relatorio->status}");
        $this->info("   Progresso: {$relatorio->progresso}%");
        $this->info("   Atualizações: " . $relatorio->atualizacoes->count());
        
        // Mostrar detalhes das atualizações
        foreach ($relatorio->atualizacoes as $atualizacao) {
            $this->info("   └─ Progresso: {$atualizacao->progresso_anterior}% → {$atualizacao->progresso_novo}%");
            $this->info("      Status: {$atualizacao->status_anterior} → {$atualizacao->status_novo}");
        }
        
        try {
            $this->info("\n🔄 Gerando PDF Snappy...");
            
            // Gerar PDF usando o template Snappy
            $pdf = SnappyPdf::loadView('relatorios.pdf-snappy', [
                'relatorio' => $relatorio
            ]);
            
            $pdf->setOption('page-size', 'A4');
            $pdf->setOption('margin-top', '10mm');
            $pdf->setOption('margin-right', '10mm');
            $pdf->setOption('margin-bottom', '10mm');
            $pdf->setOption('margin-left', '10mm');
            $pdf->setOption('encoding', 'UTF-8');
            $pdf->setOption('no-outline', true);
            $pdf->setOption('enable-local-file-access', true);
            
            $outputPath = storage_path('app/public/teste-pdf-melhorias.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Verificar se o arquivo foi criado
            if (file_exists($outputPath)) {
                $this->info("✅ Arquivo existe e pode ser aberto!");
                
                // Tentar abrir o arquivo (Windows)
                if (PHP_OS_FAMILY === 'Windows') {
                    $this->info("🖥️  Tentando abrir o arquivo...");
                    exec("start \"\" \"{$outputPath}\"");
                }
            } else {
                $this->error("❌ Arquivo não foi criado!");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar PDF: " . $e->getMessage());
            $this->error("📋 Stack trace: " . $e->getTraceAsString());
        }
    }
} 
