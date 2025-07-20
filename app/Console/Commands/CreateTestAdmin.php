<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-admin {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um usuário administrador para testes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@teste.com';
        $password = $this->argument('password') ?: 'admin123';

        // Verificar se o usuário já existe
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            // Atualizar usuário existente para admin
            $existingUser->update([
                'role' => 'admin',
                'ativo' => true,
                'password' => Hash::make($password),
            ]);
            
            $this->info("Usuário existente atualizado para administrador!");
            $this->info("Email: {$email}");
            $this->info("Senha: {$password}");
            $this->info("Nome: {$existingUser->name}");
        } else {
            // Criar novo usuário admin
            $user = User::create([
                'name' => 'Administrador de Teste',
                'email' => $email,
                'password' => Hash::make($password),
                'setor' => 'Tecnologia da Informação',
                'cargo' => 'Administrador de Teste',
                'telefone' => '(11) 99999-9999',
                'role' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->info("Usuário administrador criado com sucesso!");
            $this->info("Email: {$email}");
            $this->info("Senha: {$password}");
            $this->info("Nome: {$user->name}");
        }

        $this->info("\nCredenciais para login:");
        $this->info("URL: http://localhost:8000/login");
        $this->info("Email: {$email}");
        $this->info("Senha: {$password}");
        
        $this->info("\nFuncionalidades disponíveis para admin:");
        $this->info("- Gerenciar todos os relatórios");
        $this->info("- Gerenciar usuários");
        $this->info("- Gerenciar equipamentos");
        $this->info("- Gerenciar setores");
        $this->info("- Acessar todas as funcionalidades do sistema");
    }
} 