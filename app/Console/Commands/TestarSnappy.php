<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestarSnappy extends Command
{
    protected $signature = 'testar:snappy {relatorio_id?}';
    protected $description = 'Testa Snappy PDF';

    public function handle()
    {
        $relatorioId = $this->argument('relatorio_id');
        
        if ($relatorioId) {
            $relatorio = \App\Models\Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->find($relatorioId);
        } else {
            $relatorio = \App\Models\Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'setor', 'autor'])
                ->first();
        }
        
        if (!$relatorio) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }
        
        $this->info("🎯 Testando Snappy para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        
        $this->info('🧪 Testando Snappy...');
        
        // Verificar se Snappy está instalado
        if (!class_exists('Barryvdh\Snappy\Facades\SnappyPdf')) {
            $this->error('❌ Snappy não está instalado!');
            return;
        }
        
        $this->info('✅ Snappy está instalado');
        
        // Verificar wkhtmltopdf
        $this->info('🔍 Verificando wkhtmltopdf...');
        
        $possiblePaths = [
            '/usr/local/bin/wkhtmltopdf',
            '/usr/bin/wkhtmltopdf',
            '/usr/local/bin/wkhtmltopdf-amd64',
            '/usr/bin/wkhtmltopdf-amd64'
        ];
        
        $wkhtmltopdfPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $wkhtmltopdfPath = $path;
                break;
            }
        }
        
        if ($wkhtmltopdfPath) {
            $this->info("✅ wkhtmltopdf encontrado em: {$wkhtmltopdfPath}");
            
            // Testar versão
            $version = shell_exec("{$wkhtmltopdfPath} --version 2>&1");
            $this->info("📋 Versão: " . trim($version));
        } else {
            $this->warn("⚠️  wkhtmltopdf não encontrado");
        }
        
        // Testar geração de PDF simples
        $this->info('🔄 Gerando PDF de teste...');
        
        try {
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Teste Snappy</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; }
                    .teste { background: #f0f0f0; padding: 10px; margin: 10px 0; }
                </style>
            </head>
            <body>
                <h1>🧪 Teste Snappy PDF</h1>
                <div class="teste">
                    <p><strong>Data:</strong> ' . now()->format('d/m/Y H:i:s') . '</p>
                    <p><strong>Servidor:</strong> CloudPanel</p>
                    <p><strong>Status:</strong> ✅ Funcionando</p>
                </div>
                <p>Se você está vendo este PDF, o Snappy está funcionando perfeitamente!</p>
            </body>
            </html>';
            
            // Testar com template real
            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('relatorios.pdf-snappy', [
                'relatorio' => $relatorio
            ]);
            $pdf->setOption('page-size', 'A4');
            $pdf->setOption('margin-top', '10mm');
            $pdf->setOption('margin-right', '10mm');
            $pdf->setOption('margin-bottom', '10mm');
            $pdf->setOption('margin-left', '10mm');
            $pdf->setOption('encoding', 'UTF-8');
            
            if ($wkhtmltopdfPath) {
                $pdf->setBinary($wkhtmltopdfPath);
            }
            
            $outputPath = storage_path('app/public/teste-snappy-simples.pdf');
            $pdf->save($outputPath);
            
            if (file_exists($outputPath)) {
                $this->info("✅ PDF gerado com sucesso!");
                $this->info("📁 Arquivo: {$outputPath}");
                $this->info("📏 Tamanho: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
                $this->info("🎉 Snappy funcionando no CloudPanel!");
            } else {
                $this->error("❌ Erro ao gerar PDF");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro: " . $e->getMessage());
        }
        
        $this->info("\n📋 Resumo:");
        $this->info("   • Snappy: " . (class_exists('Barryvdh\Snappy\Facades\SnappyPdf') ? '✅ Instalado' : '❌ Não instalado'));
        $this->info("   • wkhtmltopdf: " . ($wkhtmltopdfPath ? "✅ {$wkhtmltopdfPath}" : '❌ Não encontrado'));
    }
} 
