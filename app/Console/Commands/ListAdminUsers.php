<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:admin-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista todos os usuários administradores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        if ($adminUsers->isEmpty()) {
            $this->warn('Nenhum usuário administrador encontrado.');
            $this->info('Use o comando: php artisan create:test-admin');
            return;
        }

        $this->info('Usuários Administradores:');
        $this->info('========================');
        
        foreach ($adminUsers as $user) {
            $this->info("ID: {$user->id}");
            $this->info("Nome: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Setor: {$user->setor}");
            $this->info("Cargo: {$user->cargo}");
            $this->info("Status: " . ($user->ativo ? 'Ativo' : 'Inativo'));
            $this->info("Criado em: {$user->created_at->format('d/m/Y H:i')}");
            $this->info('---');
        }

        $this->info('Total de administradores: ' . $adminUsers->count());
        
        $this->info("\nPara criar um novo admin:");
        $this->info('php artisan create:test-admin [email] [senha]');
        
        $this->info("\nPara acessar o sistema:");
        $this->info('URL: http://localhost:8000/login');
    }
} 