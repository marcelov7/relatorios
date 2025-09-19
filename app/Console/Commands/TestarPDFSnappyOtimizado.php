<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\Snappy\Facades\SnappyPdf;

class TestarPDFSnappyOtimizado extends Command
{
    protected $signature = 'testar:pdf-snappy-otimizado';
    protected $description = 'Testa a geração do PDF com template Snappy otimizado';

    public function handle()
    {
        $this->info('🧪 Testando geração do PDF Snappy Otimizado...');
        
        // Verificar se o Snappy está configurado
        try {
            $binary = config('snappy.pdf.binary');
            $this->info("📁 Binary path: $binary");
            
            if (!file_exists($binary)) {
                $this->warn('⚠️  wkhtmltopdf não encontrado no caminho configurado!');
                $this->info('💡 Execute: php artisan configurar:wkhtml');
                return;
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro ao verificar configuração do Snappy: ' . $e->getMessage());
            return;
        }
        
        // Buscar relatórios com imagens
        $relatorios = Relatorio::with(['autor', 'equipamentosTeste', 'atualizacoes.usuario'])
            ->whereNotNull('images')
            ->take(2)
            ->get();
        
        $this->info("📊 Relatórios encontrados: " . $relatorios->count());
        
        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório com imagens encontrado!');
            return;
        }
        
        // Dados para o PDF
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => 'Sistema de Gestão de Relatórios InterCement'
        ];
        
        try {
            // Gerar HTML com template otimizado
            $html = view('relatorios.pdf-snappy', $data)->render();
            
            // Configurar opções do Snappy otimizadas para imagens
            $options = [
                'page-size' => 'A4',
                'orientation' => 'portrait',
                'margin-top' => 10,
                'margin-right' => 10,
                'margin-bottom' => 10,
                'margin-left' => 10,
                'encoding' => 'UTF-8',
                'no-outline' => true,
                'enable-local-file-access' => true,
                'dpi' => 300,
                'image-quality' => 100,
                'image-dpi' => 300,
                'enable-smart-shrinking' => true,
                'print-media-type' => true,
                'zoom' => 1.0,
                'disable-smart-shrinking' => false,
            ];
            
            $this->info('⚙️  Configurando opções otimizadas do Snappy...');
            $this->info('   - DPI: 300');
            $this->info('   - Qualidade de imagem: 100%');
            $this->info('   - Smart shrinking: habilitado');
            $this->info('   - Print media type: habilitado');
            $this->info('   - Zoom: 1.0');
            
            // Gerar PDF com Snappy
            $pdf = SnappyPdf::loadHTML($html)
                ->setOptions($options)
                ->setPaper('a4')
                ->setOrientation('portrait');
            
            // Salvar arquivo
            $caminho = storage_path('app/test-pdf-snappy-otimizado.pdf');
            $pdf->save($caminho);
            
            $this->info('✅ PDF Snappy Otimizado gerado com sucesso!');
            $this->info("📁 Arquivo salvo em: $caminho");
            $this->newLine();
            
            $this->info('🎯 Para visualizar:');
            $this->info("   1. Abra o arquivo: $caminho");
            $this->info('   2. Verifique se as imagens estão com alta qualidade');
            $this->info('   3. Confirme se o layout está bem formatado');
            $this->info('   4. Compare com outros templates');
            $this->newLine();
            
            $this->info('📋 Relatórios incluídos no teste:');
            foreach ($relatorios as $relatorio) {
                $this->info("   - {$relatorio->titulo} ({$relatorio->status})");
                if (is_array($relatorio->images)) {
                    $this->info("     📸 Imagens: " . count($relatorio->images));
                }
            }
            
            $this->newLine();
            $this->info('🎨 Otimizações do template Snappy:');
            $this->info('   - Grid responsivo com auto-fit');
            $this->info('   - Imagens com object-fit: contain');
            $this->info('   - Sombras e bordas suaves');
            $this->info('   - Gradientes modernos');
            $this->info('   - Layout adaptativo');
            $this->info('   - Melhor renderização de imagens');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar PDF com Snappy Otimizado: ' . $e->getMessage());
            $this->error('📝 Stack trace: ' . $e->getTraceAsString());
            
            $this->newLine();
            $this->info('🔧 Solução de problemas:');
            $this->info('   1. Verifique se wkhtmltopdf está instalado');
            $this->info('   2. Execute: php artisan configurar:wkhtml');
            $this->info('   3. Teste o comando: wkhtmltopdf --version');
        }
    }
} 