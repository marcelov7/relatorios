<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar Tenant Principal
        $tenant1 = Tenant::firstOrCreate(
            ['slug' => 'empresa-principal'],
            [
                'nome' => 'Empresa Principal',
                'documento' => '12.345.678/0001-90',
                'ativo' => true,
            ]
        );

        // 2. Criar Tenant Secundário/Demo
        $tenant2 = Tenant::firstOrCreate(
            ['slug' => 'empresa-demo'],
            [
                'nome' => 'Empresa Demo / Secundária',
                'documento' => '98.765.432/0001-10',
                'ativo' => true,
            ]
        );

        // 3. Super Admin (Acesso global a todos os tenants)
        User::withoutTenancy()->updateOrCreate(
            ['email' => 'superadmin@sistema.com'],
            [
                'tenant_id' => null,
                'name' => 'Super Administrador',
                'password' => Hash::make('admin123'),
                'setor' => 'Gestão Global',
                'cargo' => 'Super Admin SaaS',
                'telefone' => '(11) 99999-0000',
                'role' => 'super_admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Admin do Tenant 1
        User::withoutTenancy()->updateOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'tenant_id' => $tenant1->id,
                'name' => 'Administrador (Empresa Principal)',
                'password' => Hash::make('admin123'),
                'setor' => 'TI',
                'cargo' => 'Admin de TI',
                'telefone' => '(11) 99999-1111',
                'role' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. Admin do Tenant 2
        User::withoutTenancy()->updateOrCreate(
            ['email' => 'admin@empresa-demo.com'],
            [
                'tenant_id' => $tenant2->id,
                'name' => 'Administrador (Empresa Demo)',
                'password' => Hash::make('admin123'),
                'setor' => 'TI',
                'cargo' => 'Admin Demo',
                'telefone' => '(11) 99999-2222',
                'role' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
