<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador de teste
        User::updateOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Administrador do Sistema',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'setor' => 'TI',
                'cargo' => 'Administrador',
                'telefone' => '(11) 99999-9999',
                'role' => 'admin',
                'ativo' => true,
            ]
        );

        // Criar usuário administrador alternativo
        User::updateOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Admin Teste',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'setor' => 'TI',
                'cargo' => 'Administrador',
                'telefone' => '(11) 88888-8888',
                'role' => 'admin',
                'ativo' => true,
            ]
        );

        // Criar usuário comum de teste
        User::updateOrCreate(
            ['email' => 'joao.silva@empresa.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'setor' => 'Operações',
                'cargo' => 'Operador',
                'telefone' => '(11) 77777-7777',
                'role' => 'user',
                'ativo' => true,
            ]
        );

        // Criar usuário visualizador
        User::updateOrCreate(
            ['email' => 'maria.santos@empresa.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'setor' => 'Qualidade',
                'cargo' => 'Analista',
                'telefone' => '(11) 66666-6666',
                'role' => 'user',
                'ativo' => true,
            ]
        );
    }
}