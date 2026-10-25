<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            [
                'nome' => 'Administração',
                'descricao' => 'Setor administrativo e escritórios',
                'ativo' => true,
            ],
            [
                'nome' => 'Galpão de Produção A',
                'descricao' => 'Área principal de produção com máquinas CNC',
                'ativo' => true,
            ],
            [
                'nome' => 'Galpão de Produção B',
                'descricao' => 'Área secundária de produção e montagem',
                'ativo' => true,
            ],
            [
                'nome' => 'Almoxarifado',
                'descricao' => 'Área de armazenamento de materiais e peças',
                'ativo' => true,
            ],
            [
                'nome' => 'Manutenção',
                'descricao' => 'Oficina de manutenção e reparos',
                'ativo' => true,
            ],
            [
                'nome' => 'Laboratório',
                'descricao' => 'Laboratório de testes e qualidade',
                'ativo' => true,
            ],
            [
                'nome' => 'Energia',
                'descricao' => 'Casa de força e geradores',
                'ativo' => true,
            ],
        ];

        foreach ($setores as $setor) {
            Setor::create($setor);
        }
    }
}