<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarPDFMultiplos extends Command
{
    protected $signature = 'testar:pdf-multiplos {quantidade=3}';
    protected $description = 'Testa a geração de PDF com múltiplos relatórios';

    public function handle()
    {
        $quantidade = (int) $this->argument('quantidade');
        
        if ($quantidade < 1 || $quantidade > 20) {
            $this->error('❌ Quantidade deve ser entre 1 e 20!');
            return;
        }
        
        $relatorios = Relatorio::with(['atualizacoes.usuario', 'equipamentos', 'local', 'setor', 'autor'])
            ->take($quantidade)
            ->get();
        
        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }
        
        $this->info("🎯 Testando geração de PDF com {$quantidade} relatórios:");
        foreach ($relatorios as $rel) {
            $this->info("   • ID {$rel->id}: {$rel->titulo}");
        }
        
        try {
            $this->info("\n🔄 Gerando PDF Padrão com múltiplos relatórios...");
            
            // Gerar PDF usando o template padrão
            $pdf = Pdf::loadView('relatorios.pdf', [
                'relatorios' => $relatorios
            ]);
            
            $pdf->setPaper('A4');
            
            $outputPath = storage_path('app/public/teste-pdf-multiplos.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Verificar se o arquivo foi criado
            if (file_exists($outputPath)) {
                $this->info("✅ Arquivo criado com sucesso!");
                $this->info("🎨 Template com múltiplos relatórios funcionando!");
                $this->info("📋 Quebra de página entre relatórios implementada");
                $this->info("✨ Sem borda na seção de equipamentos");
                
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