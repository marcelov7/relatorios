<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class LimparCacheCloudPanel extends Command
{
    protected $signature = 'limpar:cache-cloudpanel';
    protected $description = 'Limpa cache e configurações para CloudPanel';

    public function handle()
    {
        $this->info('🧹 Limpando cache e configurações para CloudPanel...');
        
        try {
            // Limpar cache de rotas
            $this->info('📋 Limpando cache de rotas...');
            Artisan::call('route:clear');
            $this->info('✅ Cache de rotas limpo');
            
            // Limpar cache de configuração
            $this->info('⚙️  Limpando cache de configuração...');
            Artisan::call('config:clear');
            $this->info('✅ Cache de configuração limpo');
            
            // Limpar cache de aplicação
            $this->info('🔄 Limpando cache de aplicação...');
            Artisan::call('cache:clear');
            $this->info('✅ Cache de aplicação limpo');
            
            // Limpar cache de views
            $this->info('👁️  Limpando cache de views...');
            Artisan::call('view:clear');
            $this->info('✅ Cache de views limpo');
            
            // Limpar cache de compilação
            $this->info('🔨 Limpando cache de compilação...');
            Artisan::call('clear-compiled');
            $this->info('✅ Cache de compilação limpo');
            
            // Recarregar configurações
            $this->info('🔄 Recarregando configurações...');
            Artisan::call('config:cache');
            $this->info('✅ Configurações recarregadas');
            
            // Listar rotas para verificar
            $this->info('📋 Listando rotas disponíveis...');
            $rotas = \Illuminate\Support\Facades\Route::getRoutes();
            $rotasPDF = [];
            
            foreach ($rotas as $rota) {
                $uri = $rota->uri();
                if (str_contains($uri, 'pdf') || str_contains($uri, 'lote') || str_contains($uri, 'teste')) {
                    $metodos = $rota->methods();
                    $nome = $rota->getName();
                    $rotasPDF[] = [
                        'uri' => $uri,
                        'metodos' => $metodos,
                        'nome' => $nome
                    ];
                }
            }
            
            $this->info('📋 Rotas de PDF e teste encontradas:');
            foreach ($rotasPDF as $rota) {
                $metodos = implode(',', $rota['metodos']);
                $this->info("   • {$metodos} {$rota['uri']} -> {$rota['nome']}");
            }
            
            $this->info('✅ Limpeza concluída com sucesso!');
            $this->info('💡 Agora teste as URLs:');
            $this->info('   • https://app.devaxis.com.br/teste-rota');
            $this->info('   • https://app.devaxis.com.br/teste-pdf?ids=85,84');
            $this->info('   • https://app.devaxis.com.br/gerar-pdf?ids=85,84&template=padrao');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro durante a limpeza: ' . $e->getMessage());
        }
    }
} 