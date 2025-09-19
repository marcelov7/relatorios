<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigurarSnappyCloudPanel extends Command
{
    protected $signature = 'configurar:snappy-cloudpanel';
    protected $description = 'Configura Snappy para CloudPanel';

    public function handle()
    {
        $this->info('🔧 Configurando Snappy para CloudPanel...');
        
        // Verificar se o Snappy está instalado
        if (!class_exists('Barryvdh\Snappy\Facades\SnappyPdf')) {
            $this->error('❌ Snappy não está instalado! Execute: composer require barryvdh/laravel-snappy');
            return;
        }
        
        $this->info('✅ Snappy está instalado');
        
        // Verificar se o arquivo de configuração existe
        $configPath = config_path('snappy.php');
        if (!File::exists($configPath)) {
            $this->info('📋 Publicando arquivo de configuração...');
            $this->call('vendor:publish', [
                '--provider' => 'Barryvdh\Snappy\ServiceProvider',
                '--tag' => 'config'
            ]);
        }
        
        // Verificar wkhtmltopdf
        $this->info('🔍 Verificando wkhtmltopdf...');
        
        $possiblePaths = [
            '/usr/local/bin/wkhtmltopdf',
            '/usr/bin/wkhtmltopdf',
            '/opt/homebrew/bin/wkhtmltopdf',
            '/usr/local/bin/wkhtmltopdf-amd64',
            '/usr/local/bin/wkhtmltopdf-i386',
            '/usr/bin/wkhtmltopdf-amd64',
            '/usr/bin/wkhtmltopdf-i386',
            '/usr/local/bin/wkhtmltopdf.sh',
            '/usr/bin/wkhtmltopdf.sh'
        ];
        
        $wkhtmltopdfPath = null;
        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $wkhtmltopdfPath = $path;
                break;
            }
        }
        
        if ($wkhtmltopdfPath) {
            $this->info("✅ wkhtmltopdf encontrado em: {$wkhtmltopdfPath}");
            
            // Testar se funciona
            $output = shell_exec("{$wkhtmltopdfPath} --version 2>&1");
            if (str_contains($output, 'wkhtmltopdf')) {
                $this->info("✅ wkhtmltopdf funcionando: " . trim($output));
            } else {
                $this->warn("⚠️  wkhtmltopdf encontrado mas pode ter problemas: " . trim($output));
            }
        } else {
            $this->warn("⚠️  wkhtmltopdf não encontrado nos caminhos padrão");
            $this->info("💡 Tentando instalar wkhtmltopdf...");
            
            // Tentar instalar via apt (Ubuntu/Debian)
            $this->info("📦 Tentando instalar via apt...");
            $installOutput = shell_exec("apt-get update && apt-get install -y wkhtmltopdf 2>&1");
            $this->info($installOutput);
            
            // Verificar novamente
            foreach ($possiblePaths as $path) {
                if (File::exists($path)) {
                    $wkhtmltopdfPath = $path;
                    $this->info("✅ wkhtmltopdf instalado em: {$wkhtmltopdfPath}");
                    break;
                }
            }
        }
        
        // Configurar .env
        $this->info('⚙️  Configurando .env...');
        $envPath = base_path('.env');
        
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);
            
            // Adicionar configurações do Snappy se não existirem
            if (!str_contains($envContent, 'SNAPPY_PDF_BINARY')) {
                $envContent .= "\n\n# Snappy PDF Configuration\n";
                if ($wkhtmltopdfPath) {
                    $envContent .= "SNAPPY_PDF_BINARY={$wkhtmltopdfPath}\n";
                } else {
                    $envContent .= "SNAPPY_PDF_BINARY=/usr/local/bin/wkhtmltopdf\n";
                }
                $envContent .= "SNAPPY_PDF_TIMEOUT=3600\n";
                $envContent .= "SNAPPY_PDF_ENABLE_LOCAL_FILE_ACCESS=true\n";
                
                File::put($envPath, $envContent);
                $this->info("✅ Configurações do Snappy adicionadas ao .env");
            } else {
                $this->info("✅ Configurações do Snappy já existem no .env");
            }
        }
        
        // Testar geração de PDF
        $this->info('🧪 Testando geração de PDF com Snappy...');
        
        try {
            $relatorio = \App\Models\Relatorio::first();
            if (!$relatorio) {
                $this->warn("⚠️  Nenhum relatório encontrado para teste");
                return;
            }
            
            $this->info("📋 Testando com relatório: {$relatorio->titulo}");
            
            // Gerar PDF usando Snappy
            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('relatorios.pdf-snappy', [
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
            
            // Definir caminho do binary se encontrado
            if ($wkhtmltopdfPath) {
                $pdf->setBinary($wkhtmltopdfPath);
            }
            
            $outputPath = storage_path('app/public/teste-snappy-cloudpanel.pdf');
            $pdf->save($outputPath);
            
            if (File::exists($outputPath)) {
                $this->info("✅ PDF gerado com sucesso!");
                $this->info("📁 Arquivo salvo em: {$outputPath}");
                $this->info("📏 Tamanho: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
                $this->info("🎉 Snappy configurado e funcionando no CloudPanel!");
            } else {
                $this->error("❌ Erro ao gerar PDF");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao testar Snappy: " . $e->getMessage());
            $this->info("💡 Tentando fallback para DomPDF...");
            
            // Testar com DomPDF como fallback
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('relatorios.pdf', [
                    'relatorio' => $relatorio
                ]);
                
                $outputPath = storage_path('app/public/teste-dompdf-fallback.pdf');
                $pdf->save($outputPath);
                
                $this->info("✅ Fallback para DomPDF funcionou!");
                $this->info("📁 Arquivo salvo em: {$outputPath}");
            } catch (\Exception $e2) {
                $this->error("❌ Fallback também falhou: " . $e2->getMessage());
            }
        }
        
        $this->info("\n📋 Resumo da configuração:");
        $this->info("   • Snappy: " . (class_exists('Barryvdh\Snappy\Facades\SnappyPdf') ? '✅ Instalado' : '❌ Não instalado'));
        $this->info("   • wkhtmltopdf: " . ($wkhtmltopdfPath ? "✅ {$wkhtmltopdfPath}" : '❌ Não encontrado'));
        $this->info("   • DomPDF: " . (class_exists('Barryvdh\DomPDF\Facade\Pdf') ? '✅ Disponível' : '❌ Não disponível'));
    }
} 