<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar usuário administrador se não existir
        $adminUser = User::where('email', 'admin@sistema.com')->first();
        
        if (!$adminUser) {
            User::create([
                'name' => 'Administrador do Sistema',
                'email' => 'admin@sistema.com',
                'password' => Hash::make('admin123'),
                'setor' => 'Tecnologia da Informação',
                'cargo' => 'Administrador do Sistema',
                'telefone' => '(11) 99999-9999',
                'role' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Usuário administrador criado com sucesso!');
            $this->command->info('Email: admin@sistema.com');
            $this->command->info('Senha: admin123');
        } else {
            // Atualizar usuário existente para ter os novos campos
            $adminUser->update([
                'setor' => $adminUser->setor ?: 'Tecnologia da Informação',
                'cargo' => $adminUser->cargo ?: 'Administrador do Sistema',
                'telefone' => $adminUser->telefone ?: '(11) 99999-9999',
                'role' => 'admin',
                'ativo' => true,
            ]);
            
            $this->command->info('Usuário administrador atualizado com sucesso!');
        }
        
        // Criar alguns usuários de exemplo
        $users = [
            [
                'name' => 'João Silva',
                'email' => 'joao.silva@empresa.com',
                'password' => Hash::make('123456'),
                'setor' => 'Produção',
                'cargo' => 'Operador de Máquinas',
                'telefone' => '(11) 98888-1111',
                'role' => 'user',
                'ativo' => true,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@empresa.com',
                'password' => Hash::make('123456'),
                'setor' => 'Manutenção',
                'cargo' => 'Técnica em Manutenção',
                'telefone' => '(11) 97777-2222',
                'role' => 'user',
                'ativo' => true,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@empresa.com',
                'password' => Hash::make('123456'),
                'setor' => 'Qualidade',
                'cargo' => 'Analista de Qualidade',
                'telefone' => '(11) 96666-3333',
                'role' => 'user',
                'ativo' => true,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@empresa.com',
                'password' => Hash::make('123456'),
                'setor' => 'Logística',
                'cargo' => 'Coordenadora de Logística',
                'telefone' => '(11) 95555-4444',
                'role' => 'user',
                'ativo' => false, // Usuário inativo para demonstração
            ],
        ];
        
        foreach ($users as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                User::create(array_merge($userData, [
                    'email_verified_at' => now(),
                ]));
                $this->command->info("Usuário {$userData['name']} criado com sucesso!");
            } else {
                // Atualizar usuário existente com novos campos
                $existingUser->update([
                    'setor' => $existingUser->setor ?: $userData['setor'],
                    'cargo' => $existingUser->cargo ?: $userData['cargo'],
                    'telefone' => $existingUser->telefone ?: $userData['telefone'],
                    'role' => $existingUser->role ?: $userData['role'],
                    'ativo' => $existingUser->ativo ?? $userData['ativo'],
                ]);
                $this->command->info("Usuário {$userData['name']} atualizado!");
            }
        }
    }
}
