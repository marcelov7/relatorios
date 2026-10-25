<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Barryvdh\DomPDF\Facade\Pdf;

class TestarPDFInterCement extends Command
{
    protected $signature = 'testar:pdf-intercement {relatorio_id?}';
    protected $description = 'Testa o template InterCement com equipamentos na capa';

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
        
        $this->info("🎯 Testando template InterCement para relatório: {$relatorio->titulo}");
        $this->info("   ID: {$relatorio->id}");
        $this->info("   Atividade: {$relatorio->activity}");
        $this->info("   Tipo de Atividade: " . ($relatorio->tipo_atividade ?? 'Manutenção Preventiva'));
        
        // Verificar equipamentos
        if ($relatorio->equipamentosTeste && count($relatorio->equipamentosTeste) > 0) {
            $this->info("   Equipamentos encontrados:");
            foreach ($relatorio->equipamentosTeste as $equip) {
                $this->info("     • {$equip->tag} - {$equip->nome}");
            }
        } else {
            $this->info("   ⚠️  Nenhum equipamento associado");
        }
        
        try {
            $this->info("\n🔄 Gerando PDF InterCement com equipamentos na capa...");
            
            // Gerar PDF usando o template InterCement
            $pdf = Pdf::loadView('relatorios.pdf-intercement', [
                'relatorio' => $relatorio
            ]);
            
            $pdf->setPaper('A4');
            
            $outputPath = storage_path('app/public/teste-pdf-intercement.pdf');
            $pdf->save($outputPath);
            
            $this->info("✅ PDF gerado com sucesso!");
            $this->info("📁 Arquivo salvo em: {$outputPath}");
            $this->info("📏 Tamanho do arquivo: " . number_format(filesize($outputPath) / 1024, 2) . " KB");
            
            // Verificar se o arquivo foi criado
            if (file_exists($outputPath)) {
                $this->info("✅ Arquivo criado com sucesso!");
                $this->info("🎨 Template InterCement atualizado!");
                $this->info("📋 Agora mostra equipamentos na capa");
                $this->info("✨ Design profissional com TAGs destacados");
                
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
