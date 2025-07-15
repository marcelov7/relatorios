<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@sistema.com';
        $password = $this->argument('password') ?: 'admin123';

        // Verificar se o usuário já existe
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            $this->error("Usuário com email {$email} já existe!");
            return 1;
        }

        // Criar usuário admin
        $user = User::create([
            'name' => 'Administrador do Sistema',
            'email' => $email,
            'password' => Hash::make($password),
            'setor' => 'Tecnologia da Informação',
            'cargo' => 'Administrador do Sistema',
            'telefone' => '(11) 99999-9999',
            'role' => 'admin',
            'ativo' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Usuário administrador criado com sucesso!');
        $this->info("Email: {$email}");
        $this->info("Senha: {$password}");
        $this->info("ID: {$user->id}");

        return 0;
    }
} 