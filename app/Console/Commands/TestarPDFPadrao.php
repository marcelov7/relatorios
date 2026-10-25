<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarPDFPadrao extends Command
{
    protected $signature = 'testar:pdf-padrao {relatorio_id?}';
    protected $description = 'Testa o novo template padrão baseado no InterCement';

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
        
        $this->info("🎯 Testando novo template padrão para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        $this->info("   Atividade: {$relatorio->activity}");
        $this->info("   Tipo de Atividade: " . ($relatorio->tipo_atividade ?? 'Manutenção Preventiva'));
        
        try {
            $this->info("\n🔄 Gerando PDF Padrão (baseado no InterCement)...");
            
            // Gerar PDF usando o template padrão atualizado
            $pdf = Pdf::loadView('relatorios.pdf', [
                'relatorio' => $relatorio
            ]);
            
            $pdf->setPaper('A4');
            
            $outputPath = storage_path('app/public/teste-pdf-padrao.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Verificar se o arquivo foi criado
            if (file_exists($outputPath)) {
                $this->info("✅ Arquivo criado com sucesso!");
                $this->info("🎨 Template padrão atualizado com sucesso!");
                $this->info("📋 Agora usa o mesmo modelo visual do InterCement");
                $this->info("✨ Design limpo e profissional");
                
                // Tentar abrir o arquivo (Windows)
                if (PHP_OS_FAMILY === 'Windows') {
                    $this->info("🖥️  Tentando abrir o arquivo...");
                    exec("start \"\" \"{$outputPath}\"");
                }
            } else {
                $this->error("❌ Erro ao criar o arquivo!");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar PDF: " . $e->getMessage());
        }
    }
} 
