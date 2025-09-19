<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Models\Relatorio;

class TestarRotasCloudPanel extends Command
{
    protected $signature = 'testar:rotas-cloudpanel';
    protected $description = 'Testa as rotas de PDF para CloudPanel';

    public function handle()
    {
        $this->info('🧪 Testando rotas de PDF para CloudPanel...');
        
        // Listar todas as rotas de PDF
        $this->info("\n📋 Rotas de PDF disponíveis:");
        $rotas = Route::getRoutes();
        $rotasPDF = [];
        
        foreach ($rotas as $rota) {
            $uri = $rota->uri();
            if (str_contains($uri, 'pdf') || str_contains($uri, 'lote')) {
                $metodos = $rota->methods();
                $nome = $rota->getName();
                $rotasPDF[] = [
                    'uri' => $uri,
                    'metodos' => $metodos,
                    'nome' => $nome
                ];
            }
        }
        
        foreach ($rotasPDF as $rota) {
            $metodos = implode(',', $rota['metodos']);
            $this->info("   • {$metodos} {$rota['uri']} -> {$rota['nome']}");
        }
        
        // Testar se há relatórios disponíveis
        $relatorios = Relatorio::count();
        $this->info("\n📊 Relatórios no banco: {$relatorios}");
        
        if ($relatorios > 0) {
            $primeiroRelatorio = Relatorio::first();
            $this->info("   Primeiro relatório: ID {$primeiroRelatorio->id} - {$primeiroRelatorio->titulo}");
            
            // Gerar URLs de teste
            $this->info("\n🔗 URLs de teste para CloudPanel:");
            $this->info("   • Rota principal: /relatorios/pdf-lote?ids={$primeiroRelatorio->id}&template=padrao");
            $this->info("   • Rota alternativa: /pdf-lote?ids={$primeiroRelatorio->id}&template=padrao");
            $this->info("   • InterCement: /relatorios/pdf-intercement?ids={$primeiroRelatorio->id}");
            $this->info("   • InterCement alt: /pdf-intercement?ids={$primeiroRelatorio->id}");
            
            // Testar geração de PDF
            $this->info("\n🔄 Testando geração de PDF...");
            try {
                $controller = new \App\Http\Controllers\RelatorioController();
                $request = new \Illuminate\Http\Request();
                $request->merge(['ids' => [$primeiroRelatorio->id], 'template' => 'padrao']);
                
                $response = $controller->pdfLote($request);
                
                if ($response) {
                    $this->info("✅ Controller funcionando corretamente!");
                    $this->info("📄 Response type: " . get_class($response));
                } else {
                    $this->error("❌ Controller retornou null");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Erro no controller: " . $e->getMessage());
            }
        } else {
            $this->warn("⚠️  Nenhum relatório encontrado para teste");
        }
        
        // Verificar configurações do servidor
        $this->info("\n🔧 Configurações do servidor:");
        $this->info("   • PHP Version: " . PHP_VERSION);
        $this->info("   • Laravel Version: " . app()->version());
        $this->info("   • App URL: " . config('app.url'));
        $this->info("   • App Environment: " . config('app.env'));
        
        // Verificar se o DomPDF está disponível
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $this->info("   • DomPDF: ✅ Disponível");
        } else {
            $this->error("   • DomPDF: ❌ Não encontrado");
        }
        
        // Verificar se o Snappy está disponível
        if (class_exists('Barryvdh\Snappy\Facades\SnappyPdf')) {
            $this->info("   • Snappy: ✅ Disponível");
        } else {
            $this->warn("   • Snappy: ⚠️  Não encontrado (opcional)");
        }
        
        $this->info("\n💡 Dicas para CloudPanel:");
        $this->info("   1. Verifique se o mod_rewrite está habilitado");
        $this->info("   2. Confirme se o .htaccess está sendo lido");
        $this->info("   3. Teste as rotas alternativas (/pdf-lote)");
        $this->info("   4. Verifique os logs do servidor para erros 404");
    }
} 