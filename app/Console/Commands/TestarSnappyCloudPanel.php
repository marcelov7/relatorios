<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;

class TestarSnappyCloudPanel extends Command
{
    protected $signature = 'testar:snappy-cloudpanel {relatorio_id?}';
    protected $description = 'Testa Snappy no CloudPanel';

    public function handle()
    {
        $relatorioId = $this->argument('relatorio_id');
        
        if ($relatorioId) {
            $relatorio = Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->find($relatorioId);
        } else {
            $relatorio = Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->first();
        }
        
        if (!$relatorio) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }
        
        $this->info("🎯 Testando Snappy no CloudPanel para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        
        // Verificar se Snappy está disponível
        if (!class_exists('Barryvdh\Snappy\Facades\SnappyPdf')) {
            $this->error('❌ Snappy não está instalado!');
            return;
        }
        
        $this->info('✅ Snappy está disponível');
        
        // Verificar wkhtmltopdf
        $this->info('🔍 Verificando wkhtmltopdf...');
        $wkhtmltopdfPath = config('snappy.pdf.binary', '/usr/local/bin/wkhtmltopdf');
        
        if (file_exists($wkhtmltopdfPath)) {
            $this->info("✅ wkhtmltopdf encontrado em: {$wkhtmltopdfPath}");
            
            // Testar versão
            $version = shell_exec("{$wkhtmltopdfPath} --version 2>&1");
            $this->info("📋 Versão: " . trim($version));
        } else {
            $this->warn("⚠️  wkhtmltopdf não encontrado em: {$wkhtmltopdfPath}");
            
            // Tentar encontrar em outros caminhos
            $possiblePaths = [
                '/usr/bin/wkhtmltopdf',
                '/usr/local/bin/wkhtmltopdf-amd64',
                '/usr/bin/wkhtmltopdf-amd64'
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $wkhtmltopdfPath = $path;
                    $this->info("✅ wkhtmltopdf encontrado em: {$wkhtmltopdfPath}");
                    break;
                }
            }
        }
        
        try {
            $this->info("\n🔄 Gerando PDF com Snappy...");
            
            // Gerar PDF usando Snappy
            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('relatorios.pdf-snappy', [
                'relatorio' => $relatorio
            ]);
            
            // Configurar opções
            $pdf->setOption('page-size', 'A4');
            $pdf->setOption('margin-top', '10mm');
            $pdf->setOption('margin-right', '10mm');
            $pdf->setOption('margin-bottom', '10mm');
            $pdf->setOption('margin-left', '10mm');
            $pdf->setOption('encoding', 'UTF-8');
            $pdf->setOption('no-outline', true);
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('dpi', 300);
            
            // Definir binary se encontrado
            if (file_exists($wkhtmltopdfPath)) {
                $pdf->setBinary($wkhtmltopdfPath);
            }
            
            $outputPath = storage_path('app/public/teste-snappy-cloudpanel.pdf');
            $pdf->save($outputPath);
            
            if (file_exists($outputPath)) {
                $this->info("✅ PDF gerado com sucesso!");
                $this->info("📁 Arquivo salvo em: {$outputPath}");
                $this->info("📏 Tamanho: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
                $this->info("🎉 Snappy funcionando perfeitamente no CloudPanel!");
                
                // Comparar com DomPDF
                $this->info("\n🔄 Gerando PDF com DomPDF para comparação...");
                
                $dompdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('relatorios.pdf', [
                    'relatorio' => $relatorio
                ]);
                
                $dompdfPath = storage_path('app/public/teste-dompdf-comparacao.pdf');
                $dompdf->save($dompdfPath);
                
                if (file_exists($dompdfPath)) {
                    $this->info("✅ DomPDF também funcionou!");
                    $this->info("📏 Snappy: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
                    $this->info("📏 DomPDF: " . number_format(filesize($dompdfPath) / 1024, 2) . " KB");
                    
                    if (filesize($outputPath) > filesize($dompdfPath)) {
                        $this->info("📈 Snappy gerou arquivo maior (melhor qualidade)");
                    } else {
                        $this->info("📉 DomPDF gerou arquivo maior");
                    }
                }
                
            } else {
                $this->error("❌ Erro ao gerar PDF com Snappy");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar PDF com Snappy: " . $e->getMessage());
            
            // Tentar com DomPDF como fallback
            $this->info("\n💡 Tentando fallback para DomPDF...");
            
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('relatorios.pdf', [
                    'relatorio' => $relatorio
                ]);
                
                $outputPath = storage_path('app/public/teste-dompdf-fallback.pdf');
                $pdf->save($outputPath);
                
                $this->info("✅ Fallback para DomPDF funcionou!");
                $this->info("📁 Arquivo salvo em: {$outputPath}");
                $this->info("📏 Tamanho: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
                
            } catch (\Exception $e2) {
                $this->error("❌ Fallback também falhou: " . $e2->getMessage());
            }
        }
        
        $this->info("\n📋 Resumo do teste:");
        $this->info("   • Snappy: " . (class_exists('Barryvdh\Snappy\Facades\SnappyPdf') ? '✅ Disponível' : '❌ Não disponível'));
        $this->info("   • wkhtmltopdf: " . (file_exists($wkhtmltopdfPath) ? "✅ {$wkhtmltopdfPath}" : '❌ Não encontrado'));
        $this->info("   • DomPDF: " . (class_exists('Barryvdh\DomPDF\Facade\Pdf') ? '✅ Disponível' : '❌ Não disponível'));
    }
} 
