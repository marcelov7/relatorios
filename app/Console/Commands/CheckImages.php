<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RelatorioImagem;

class CheckImages extends Command
{
    protected $signature = 'check:images';
    protected $description = 'Verifica as imagens no banco de dados';

    public function handle()
    {
        $this->info('🔍 Verificando imagens no banco de dados...');
        
        $imagens = RelatorioImagem::all();
        
        if ($imagens->isEmpty()) {
            $this->warn('❌ Nenhuma imagem encontrada no banco de dados');
            return;
        }
        
        $this->info("✅ Encontradas {$imagens->count()} imagem(s) no banco:");
        
        foreach ($imagens as $imagem) {
            $this->line("📸 ID: {$imagem->id} | Relatório: {$imagem->relatorio_id}");
            $this->line("   Original: {$imagem->caminho_original}");
            $this->line("   Thumb: {$imagem->caminho_thumb}");
            $this->line("   Medium: {$imagem->caminho_medium}");
            $this->line("   Nome: {$imagem->nome_original}");
            $this->line("   Tamanho: {$imagem->tamanho} bytes");
            $this->line("   MIME: {$imagem->mime_type}");
            $this->line("   Ordem: {$imagem->ordem}");
            $this->line("---");
        }
    }
} 