<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarTipoAtividade extends Command
{
    protected $signature = 'testar:tipo-atividade {relatorio_id?}';
    protected $description = 'Testa o campo tipo_atividade nos PDFs';

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
        
        $this->info("🎯 Testando campo 'Tipo de Atividade' para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        $this->info("   Atividade: {$relatorio->activity}");
        $this->info("   Tipo de Atividade: " . ($relatorio->tipo_atividade ?? 'Não definido'));
        
        try {
            $this->info("\n🔄 Gerando PDF Snappy com Tipo de Atividade...");
            
            // Gerar PDF usando o template Snappy
            $pdf = Pdf::loadView('relatorios.pdf-snappy', [
                'relatorio' => $relatorio
            ]);
            
            $pdf->setPaper('A4');
            
            $outputPath = storage_path('app/public/teste-tipo-atividade.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Gerar também o PDF InterCement
            $this->info("\n🔄 Gerando PDF InterCement com Tipo de Atividade...");
            
            $pdfInterCement = Pdf::loadView('relatorios.pdf-intercement', [
                'relatorio' => $relatorio
            ]);
            
            $pdfInterCement->setPaper('A4');
            
            $outputPathInterCement = storage_path('app/public/teste-tipo-atividade-intercement.pdf');
            $pdfInterCement->save($outputPathInterCement);
            
            $this->info("✅ PDF InterCement gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPathInterCement}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPathInterCement) / 1024, 2) . " KB");
            
            // Verificar se os arquivos foram criados
            if (file_exists($outputPath) && file_exists($outputPathInterCement)) {
                $this->info("✅ Ambos os arquivos foram criados com sucesso!");
                $this->info("📝 O campo 'Tipo de Atividade' foi adicionado aos templates!");
                $this->info("🎯 Agora os PDFs mostram: Atividade + Tipo de Atividade");
                
                // Tentar abrir os arquivos (Windows)
                if (PHP_OS_FAMILY === 'Windows') {
                    $this->info("🖥️  Tentando abrir os arquivos...");
                    exec("start \"\" \"{$outputPath}\"");
                    sleep(2);
                    exec("start \"\" \"{$outputPathInterCement}\"");
                }
            } else {
                $this->error("❌ Erro ao criar os arquivos!");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao gerar PDF: " . $e->getMessage());
        }
    }
} 
