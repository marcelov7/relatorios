<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfigurarWKHTML extends Command
{
    protected $signature = 'configurar:wkhtml';
    protected $description = 'Configura automaticamente o wkhtmltopdf no sistema';

    public function handle()
    {
        $this->info('🔧 Configurando wkhtmltopdf automaticamente...');
        
        // Caminhos possíveis para o wkhtmltopdf
        $possiblePaths = [
            'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'C:\Program Files (x86)\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'C:\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'wkhtmltopdf', // Se estiver no PATH
        ];
        
        $foundPath = null;
        
        foreach ($possiblePaths as $path) {
            $this->info("🔍 Verificando: $path");
            
            if ($path === 'wkhtmltopdf') {
                // Testar se está no PATH
                $output = shell_exec("where $path 2>&1");
                if (!empty($output) && strpos($output, 'wkhtmltopdf') !== false) {
                    $foundPath = $path;
                    $this->info("✅ Encontrado no PATH: $path");
                    break;
                }
            } else {
                // Verificar arquivo específico
                if (file_exists($path)) {
                    $foundPath = $path;
                    $this->info("✅ Arquivo encontrado: $path");
                    break;
                }
            }
        }
        
        if (!$foundPath) {
            $this->error('❌ wkhtmltopdf não encontrado!');
            $this->newLine();
            $this->info('📥 Para instalar:');
            $this->info('   1. Baixe de: https://wkhtmltopdf.org/downloads.html');
            $this->info('   2. Instale em: C:\\Program Files\\wkhtmltopdf\\');
            $this->info('   3. Execute este comando novamente');
            $this->newLine();
            $this->info('💡 Alternativa:');
            $this->info('   - Use Chocolatey: choco install wkhtmltopdf');
            $this->info('   - Ou adicione manualmente ao PATH do sistema');
            return;
        }
        
        // Testar se funciona
        $this->info('🧪 Testando execução...');
        $testCommand = $foundPath === 'wkhtmltopdf' ? 'wkhtmltopdf --version' : "\"$foundPath\" --version";
        $output = shell_exec($testCommand . ' 2>&1');
        
        if (strpos($output, 'wkhtmltopdf') !== false) {
            $this->info('✅ wkhtmltopdf funcionando!');
            $this->info("📋 Versão: " . trim($output));
        } else {
            $this->error('❌ Erro ao executar wkhtmltopdf');
            $this->error("📝 Saída: $output");
            return;
        }
        
        // Configurar no .env
        $this->info('⚙️  Configurando no .env...');
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            $this->error('❌ Arquivo .env não encontrado!');
            return;
        }
        
        $envContent = file_get_contents($envPath);
        
        // Verificar se já existe a configuração
        if (strpos($envContent, 'WKHTML_PDF_BINARY') !== false) {
            // Atualizar configuração existente
            $envContent = preg_replace(
                '/WKHTML_PDF_BINARY=.*/',
                "WKHTML_PDF_BINARY=\"$foundPath\"",
                $envContent
            );
            $this->info('🔄 Configuração atualizada no .env');
        } else {
            // Adicionar nova configuração
            $envContent .= "\nWKHTML_PDF_BINARY=\"$foundPath\"\n";
            $this->info('➕ Configuração adicionada ao .env');
        }
        
        file_put_contents($envPath, $envContent);
        
        // Limpar cache de configuração
        $this->call('config:clear');
        $this->call('config:cache');
        
        $this->info('✅ Configuração concluída!');
        $this->newLine();
        $this->info('🎯 Próximos passos:');
        $this->info('   1. Teste: php artisan testar:pdf-snappy');
        $this->info('   2. Use o botão "PDF Snappy" na interface');
        $this->info('   3. Compare a qualidade com DomPDF');
        
        $this->newLine();
        $this->info('📋 Configuração atual:');
        $this->info("   Binary: $foundPath");
        $this->info('   Status: ✅ Funcionando');
    }
} 