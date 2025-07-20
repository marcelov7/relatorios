<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:server {--port=8000} {--host=0.0.0.0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia o servidor de desenvolvimento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = $this->option('port');
        $host = $this->option('host');
        
        $this->info('🚀 Iniciando servidor de desenvolvimento...');
        $this->info("📡 Host: {$host}");
        $this->info("🔌 Porta: {$port}");
        $this->info('🌐 URL: http://localhost:' . $port);
        
        $this->info("\n📋 Credenciais de teste:");
        $this->info('👨‍💼 Admin: admin@teste.com / admin123');
        $this->info('👥 Usuário: joao.silva@empresa.com / 123456');
        
        $this->info("\n🎯 Para testar as melhorias:");
        $this->info('1. Acesse: http://localhost:' . $port . '/login');
        $this->info('2. Faça login com as credenciais acima');
        $this->info('3. Vá para: http://localhost:' . $port . '/relatorios');
        $this->info('4. Teste os filtros por setor, autor e paginação');
        
        $this->info("\n⏹️  Para parar o servidor: Ctrl+C");
        $this->info('📖 Para mais informações: CREDENCIAIS-TESTE.md');
        
        $this->info("\n🔄 Iniciando servidor...");
        
        // Executar o comando serve
        $command = "php artisan serve --host={$host} --port={$port}";
        $this->info("Executando: {$command}");
        
        passthru($command);
    }
} 