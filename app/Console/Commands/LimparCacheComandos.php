<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LimparCacheComandos extends Command
{
    protected $signature = 'limpar:comandos';
    protected $description = 'Limpa cache e recarrega comandos';

    public function handle()
    {
        $this->info('🧹 Limpando cache e recarregando comandos...');
        
        // Limpar cache de configuração
        $this->call('config:clear');
        $this->info('✅ Cache de configuração limpo');
        
        // Limpar cache de rotas
        $this->call('route:clear');
        $this->info('✅ Cache de rotas limpo');
        
        // Limpar cache de views
        $this->call('view:clear');
        $this->info('✅ Cache de views limpo');
        
        // Limpar cache de aplicação
        $this->call('cache:clear');
        $this->info('✅ Cache de aplicação limpo');
        
        // Recarregar configurações
        $this->call('config:cache');
        $this->info('✅ Configurações recarregadas');
        
        // Verificar comandos disponíveis
        $this->info('📋 Comandos disponíveis:');
        $this->call('list');
        
        $this->info('🎉 Cache limpo e comandos recarregados!');
    }
} 