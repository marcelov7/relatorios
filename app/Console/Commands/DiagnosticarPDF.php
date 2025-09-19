<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Illuminate\Support\Facades\Log;

class DiagnosticarPDF extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'pdf:diagnosticar {relatorio_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Diagnostica problemas de geração de PDF no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 DIAGNÓSTICO DO SISTEMA PDF');
        $this->info('================================');

        // 1. Verificar ambiente
        $this->checkEnvironment();

        // 2. Verificar Chromium/Chrome
        $this->checkChrome();

        // 3. Verificar permissões
        $this->checkPermissions();

        // 4. Verificar templates
        $this->checkTemplates();

        // 5. Testar geração de PDF (se ID fornecido)
        $relatorioId = $this->argument('relatorio_id');
        if ($relatorioId) {
            $this->testPdfGeneration($relatorioId);
        }

        $this->info('================================');
        $this->info('✅ Diagnóstico concluído!');
    }

    private function checkEnvironment()
    {
        $this->info('🌍 AMBIENTE:');
        $this->line('- PHP OS: ' . PHP_OS);
        $this->line('- Laravel Environment: ' . app()->environment());
        $this->line('- PHP Version: ' . phpversion());
        $this->line('- Laravel Version: ' . app()->version());
        
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $this->line('- É Windows: ' . ($isWindows ? 'Sim' : 'Não'));
        $this->newLine();
    }

    private function checkChrome()
    {
        $this->info('🌐 CHROME/CHROMIUM:');
        
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        if ($isWindows) {
            $chromePaths = [
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe'
            ];
        } else {
            $chromePaths = [
                '/snap/bin/chromium',
                '/usr/bin/chromium-browser',
                '/usr/bin/chromium',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/google-chrome'
            ];
        }

        $found = false;
        foreach ($chromePaths as $path) {
            $exists = file_exists($path);
            $this->line('- ' . $path . ': ' . ($exists ? '✅ Encontrado' : '❌ Não encontrado'));
            if ($exists && !$found) {
                $found = true;
                
                // Testar execução
                try {
                    $version = shell_exec($path . ' --version 2>&1');
                    $this->line('  Versão: ' . trim($version));
                } catch (\Exception $e) {
                    $this->line('  ❌ Erro ao executar: ' . $e->getMessage());
                }
            }
        }

        if (!$found) {
            $this->error('❌ Nenhum Chrome/Chromium encontrado!');
        }
        
        $this->newLine();
    }

    private function checkPermissions()
    {
        $this->info('🔒 PERMISSÕES:');
        
        // Verificar diretório storage
        $storagePath = storage_path();
        $this->line('- Storage path: ' . $storagePath);
        $this->line('- Storage writable: ' . (is_writable($storagePath) ? '✅ Sim' : '❌ Não'));

        // Verificar /tmp
        $tmpPath = '/tmp';
        if (is_dir($tmpPath)) {
            $this->line('- /tmp exists: ✅ Sim');
            $this->line('- /tmp writable: ' . (is_writable($tmpPath) ? '✅ Sim' : '❌ Não'));
            
            // Testar criação de diretório temporário
            $testDir = $tmpPath . '/browsershot-test-' . uniqid();
            try {
                mkdir($testDir, 0755, true);
                $this->line('- Criar dir temporário: ✅ Sucesso');
                rmdir($testDir);
            } catch (\Exception $e) {
                $this->line('- Criar dir temporário: ❌ Erro - ' . $e->getMessage());
            }
        } else {
            $this->line('- /tmp exists: ❌ Não');
        }

        $this->newLine();
    }

    private function checkTemplates()
    {
        $this->info('📄 TEMPLATES:');
        
        $templates = [
            'relatorios.pdf-browsershot',
            'relatorios.pdf-spatie'
        ];

        foreach ($templates as $template) {
            try {
                $path = resource_path('views/' . str_replace('.', '/', $template) . '.blade.php');
                $exists = file_exists($path);
                $this->line('- ' . $template . ': ' . ($exists ? '✅ Existe' : '❌ Não existe'));
                
                if ($exists) {
                    $size = filesize($path);
                    $this->line('  Tamanho: ' . number_format($size) . ' bytes');
                }
            } catch (\Exception $e) {
                $this->line('- ' . $template . ': ❌ Erro - ' . $e->getMessage());
            }
        }

        $this->newLine();
    }

    private function testPdfGeneration($relatorioId)
    {
        $this->info('🧪 TESTE DE GERAÇÃO PDF:');
        $this->line('- Relatório ID: ' . $relatorioId);

        try {
            $relatorio = Relatorio::findOrFail($relatorioId);
            $this->line('- Relatório encontrado: ✅ Sim');
            $this->line('- Título: ' . $relatorio->titulo);
            $this->line('- Status: ' . $relatorio->status);

            // Simular chamada do controller
            $this->line('- Testando geração...');
            
            $controller = new \App\Http\Controllers\RelatorioController();
            
            // Capturar logs
            Log::info('Teste de PDF iniciado via comando Artisan', ['relatorio_id' => $relatorioId]);
            
            $result = $controller->pdfBrowsershot($relatorioId);
            
            if ($result instanceof \Illuminate\Http\Response) {
                $this->line('- Resultado: ✅ PDF gerado com sucesso');
                $this->line('- Content-Type: ' . $result->headers->get('Content-Type'));
                $this->line('- Tamanho: ' . strlen($result->getContent()) . ' bytes');
            } else {
                $this->line('- Resultado: ❌ Resposta inesperada');
                $this->line('- Tipo: ' . gettype($result));
            }

        } catch (\Exception $e) {
            $this->error('❌ Erro no teste: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
        }

        $this->newLine();
    }
}
