<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\Snappy\Facades\SnappyPdf;

class TestarPDFSnappy extends Command
{
    protected $signature = 'testar:pdf-snappy';
    protected $description = 'Testa a geração do PDF com Snappy (wkhtmltopdf)';

    public function handle()
    {
        $this->info('🧪 Testando geração do PDF com Snappy...');
        
        // Verificar se o Snappy está configurado
        try {
            $binary = config('snappy.pdf.binary');
            $this->info("📁 Binary path: $binary");
            
            if (!file_exists($binary)) {
                $this->warn('⚠️  wkhtmltopdf não encontrado no caminho configurado!');
                $this->info('💡 Para Windows, você pode:');
                $this->info('   1. Baixar wkhtmltopdf de: https://wkhtmltopdf.org/downloads.html');
                $this->info('   2. Instalar e configurar o caminho no .env');
                $this->info('   3. Ou usar o caminho padrão do sistema');
                return;
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro ao verificar configuração do Snappy: ' . $e->getMessage());
            return;
        }
        
        // Buscar relatórios
        $relatorios = Relatorio::with(['autor', 'equipamentosTeste', 'atualizacoes.usuario'])
            ->take(2)
            ->get();
        
        $this->info("📊 Relatórios encontrados: " . $relatorios->count());
        
        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }
        
        // Dados para o PDF
        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => 'Sistema de Gestão de Relatórios InterCement'
        ];
        
        try {
            // Gerar HTML
            $html = view('relatorios.pdf-intercement', $data)->render();
            
            // Configurar opções do Snappy
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
            ];
            
            $this->info('⚙️  Configurando opções do Snappy...');
            $this->info('   - DPI: 300');
            $this->info('   - Tamanho: A4');
            $this->info('   - Orientação: Portrait');
            $this->info('   - Margens: 10mm');
            $this->info('   - Encoding: UTF-8');
            
            // Gerar PDF com Snappy
            $pdf = SnappyPdf::loadHTML($html)
                ->setOptions($options)
                ->setPaper('a4')
                ->setOrientation('portrait');
            
            // Salvar arquivo
            $caminho = storage_path('app/test-pdf-snappy.pdf');
            $pdf->save($caminho);
            
            $this->info('✅ PDF Snappy gerado com sucesso!');
            $this->info("📁 Arquivo salvo em: $caminho");
            $this->newLine();
            
            $this->info('🎯 Para visualizar:');
            $this->info("   1. Abra o arquivo: $caminho");
            $this->info('   2. Verifique se as imagens estão com alta qualidade');
            $this->info('   3. Confirme se o layout está bem formatado');
            $this->info('   4. Compare com o PDF gerado pelo DomPDF');
            $this->newLine();
            
            $this->info('📋 Relatórios incluídos no teste:');
            foreach ($relatorios as $relatorio) {
                $this->info("   - {$relatorio->titulo} ({$relatorio->status})");
            }
            
            $this->newLine();
            $this->info('🎨 Vantagens do Snappy:');
            $this->info('   - Melhor renderização de imagens');
            $this->info('   - Suporte completo a CSS3');
            $this->info('   - Qualidade de impressão superior');
            $this->info('   - Melhor compatibilidade com fontes');
            $this->info('   - Suporte a JavaScript (se necessário)');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar PDF com Snappy: ' . $e->getMessage());
            $this->error('📝 Stack trace: ' . $e->getTraceAsString());
            
            $this->newLine();
            $this->info('🔧 Solução de problemas:');
            $this->info('   1. Verifique se wkhtmltopdf está instalado');
            $this->info('   2. Confirme o caminho no arquivo .env');
            $this->info('   3. Teste o comando: wkhtmltopdf --version');
        }
    }
} 