<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testar:pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a geração do PDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando geração do PDF...');

        // Buscar alguns relatórios para teste
        $relatorios = Relatorio::with(['user', 'autor', 'local', 'equipamentosTeste'])
            ->take(3)
            ->get();

        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório encontrado para teste!');
            return;
        }

        $this->info('📊 Relatórios encontrados: ' . $relatorios->count());

        $data = [
            'relatorios' => $relatorios,
            'data_geracao' => now()->format('d/m/Y H:i'),
            'nome_sistema' => config('app.name', 'Sistema de Relatórios'),
        ];

        try {
            $pdf = Pdf::loadView('relatorios.pdf', $data)->setPaper('a4', 'portrait');
            
            // Salvar o PDF para teste
            $testPath = storage_path('app/test-pdf.pdf');
            $pdf->save($testPath);
            
            $this->info('✅ PDF gerado com sucesso!');
            $this->info('📁 Arquivo salvo em: ' . $testPath);
            $this->info('');
            $this->info('🎯 Para visualizar:');
            $this->info('   1. Abra o arquivo: ' . $testPath);
            $this->info('   2. Verifique se a capa está correta');
            $this->info('   3. Confirme se o conteúdo está bem formatado');
            $this->info('');
            $this->info('📋 Relatórios incluídos no teste:');
            foreach ($relatorios as $relatorio) {
                $this->info('   - ' . $relatorio->titulo . ' (' . $relatorio->status . ')');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar PDF: ' . $e->getMessage());
            $this->error('📝 Stack trace: ' . $e->getTraceAsString());
        }
    }
} 