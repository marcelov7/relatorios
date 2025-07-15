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
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@teste.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);

        // Criar usuário comum de teste
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);
    }
} 