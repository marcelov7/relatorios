<?php

namespace Database\Seeders;

use App\Models\Local;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locais = [
            [
                'nome' => 'Prédio Principal',
                'descricao' => 'Prédio principal da empresa com escritórios administrativos',
                'setor' => 'Administrativo',
                'responsavel' => 'João Silva',
                'telefone' => '(11) 1234-5678',
                'email' => 'joao.silva@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Galpão de Produção A',
                'descricao' => 'Galpão principal de produção com linha de montagem',
                'setor' => 'Produção',
                'responsavel' => 'Maria Santos',
                'telefone' => '(11) 2345-6789',
                'email' => 'maria.santos@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Galpão de Produção B',
                'descricao' => 'Galpão secundário de produção para produtos especiais',
                'setor' => 'Produção',
                'responsavel' => 'Carlos Oliveira',
                'telefone' => '(11) 3456-7890',
                'email' => 'carlos.oliveira@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Estoque Central',
                'descricao' => 'Área de armazenamento de matérias-primas e produtos acabados',
                'setor' => 'Logística',
                'responsavel' => 'Ana Costa',
                'telefone' => '(11) 4567-8901',
                'email' => 'ana.costa@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Laboratório de Qualidade',
                'descricao' => 'Laboratório para controle de qualidade dos produtos',
                'setor' => 'Qualidade',
                'responsavel' => 'Pedro Ferreira',
                'telefone' => '(11) 5678-9012',
                'email' => 'pedro.ferreira@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Oficina de Manutenção',
                'descricao' => 'Oficina para manutenção de equipamentos e ferramentas',
                'setor' => 'Manutenção',
                'responsavel' => 'José Almeida',
                'telefone' => '(11) 6789-0123',
                'email' => 'jose.almeida@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Sala de Servidores',
                'descricao' => 'Sala com servidores e equipamentos de TI',
                'setor' => 'TI',
                'responsavel' => 'Laura Pereira',
                'telefone' => '(11) 7890-1234',
                'email' => 'laura.pereira@empresa.com',
                'ativo' => true,
            ],
            [
                'nome' => 'Refeitório',
                'descricao' => 'Área de alimentação dos funcionários',
                'setor' => 'Recursos Humanos',
                'responsavel' => 'Roberto Lima',
                'telefone' => '(11) 8901-2345',
                'email' => 'roberto.lima@empresa.com',
                'ativo' => true,
            ],
        ];

        foreach ($locais as $local) {
            Local::create($local);
        }
    }
}
