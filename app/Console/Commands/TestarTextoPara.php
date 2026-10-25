<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\Snappy\Facades\SnappyPdf;

class TestarTextoPara extends Command
{
    protected $signature = 'testar:texto-para {relatorio_id?}';
    protected $description = 'Testa o texto "para" nos PDFs';

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
        
        $this->info("🎯 Testando texto 'para' para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        $this->info("   Atualizações: " . $relatorio->atualizacoes->count());
        
        // Mostrar as mudanças que serão exibidas
        foreach ($relatorio->atualizacoes as $atualizacao) {
            $this->info("   └─ Progresso: {$atualizacao->progresso_anterior}% para {$atualizacao->progresso_novo}%");
            $this->info("      Status: {$atualizacao->status_anterior} para {$atualizacao->status_novo}");
        }
        
        try {
            $this->info("\n🔄 Gerando PDF Snappy com texto 'para'...");
            
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
            
            // Corrigir caminho do wkhtmltopdf para Windows
            if (PHP_OS_FAMILY === 'Windows') {
                $pdf->setBinary('"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"');
            }
            
            $outputPath = storage_path('app/public/teste-texto-para.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Gerar também o PDF InterCement
            $this->info("\n🔄 Gerando PDF InterCement com texto 'para'...");
            
            $pdfInterCement = SnappyPdf::loadView('relatorios.pdf-intercement', [
                'relatorio' => $relatorio
            ]);
            
            $pdfInterCement->setOption('page-size', 'A4');
            $pdfInterCement->setOption('margin-top', '10mm');
            $pdfInterCement->setOption('margin-right', '10mm');
            $pdfInterCement->setOption('margin-bottom', '10mm');
            $pdfInterCement->setOption('margin-left', '10mm');
            $pdfInterCement->setOption('encoding', 'UTF-8');
            $pdfInterCement->setOption('no-outline', true);
            $pdfInterCement->setOption('enable-local-file-access', true);
            
            // Corrigir caminho do wkhtmltopdf para Windows
            if (PHP_OS_FAMILY === 'Windows') {
                $pdfInterCement->setBinary('"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"');
            }
            
            $outputPathInterCement = storage_path('app/public/teste-texto-para-intercement.pdf');
            $pdfInterCement->save($outputPathInterCement);
            
            $this->info("✅ PDF InterCement gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPathInterCement}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPathInterCement) / 1024, 2) . " KB");
            
            // Verificar se os arquivos foram criados
            if (file_exists($outputPath) && file_exists($outputPathInterCement)) {
                $this->info("✅ Ambos os arquivos foram criados com sucesso!");
                $this->info("📝 O texto 'para' foi implementado nos templates!");
                
                // Tentar abrir os arquivos (Windows)
                if (PHP_OS_FAMILY === 'Windows') {
                    $this->info("🖥️  Tentando abrir os arquivos...");
                    exec("start \"\" \"{$outputPath}\"");
                    sleep(2);
                    exec("start \"\" \"{$outputPathInterCement}\"");
                }
            } else {
                $this->error("❌ Erro ao criar os arquivos!");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar PDF: " . $e->getMessage());
        }
    }
} 
