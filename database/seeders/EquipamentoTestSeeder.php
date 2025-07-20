<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipamentoTest;

class EquipamentoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipamentos = [
            // Setor: Produção
            [
                'tag' => 'EQPROD001',
                'nome' => 'Máquina de Corte',
                'setor' => 'Produção',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQPROD002',
                'nome' => 'Esteira Transportadora',
                'setor' => 'Produção',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQPROD003',
                'nome' => 'Prensa Hidráulica',
                'setor' => 'Produção',
                'status' => 'Manutenção',
            ],
            
            // Setor: Manutenção
            [
                'tag' => 'EQMAN001',
                'nome' => 'Compressor de Ar',
                'setor' => 'Manutenção',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQMAN002',
                'nome' => 'Gerador de Energia',
                'setor' => 'Manutenção',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQMAN003',
                'nome' => 'Sistema de Refrigeração',
                'setor' => 'Manutenção',
                'status' => 'Inativo',
            ],
            
            // Setor: Qualidade
            [
                'tag' => 'EQQUAL001',
                'nome' => 'Microscópio Digital',
                'setor' => 'Qualidade',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQQUAL002',
                'nome' => 'Balança de Precisão',
                'setor' => 'Qualidade',
                'status' => 'Operacional',
            ],
            
            // Setor: Logística
            [
                'tag' => 'EQLOG001',
                'nome' => 'Empilhadeira',
                'setor' => 'Logística',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQLOG002',
                'nome' => 'Sistema de Rastreamento',
                'setor' => 'Logística',
                'status' => 'Manutenção',
            ],
            
            // Setor: Laboratório
            [
                'tag' => 'EQLAB001',
                'nome' => 'Centrífuga',
                'setor' => 'Laboratório',
                'status' => 'Operacional',
            ],
            [
                'tag' => 'EQLAB002',
                'nome' => 'Autoclave',
                'setor' => 'Laboratório',
                'status' => 'Operacional',
            ],
        ];

        foreach ($equipamentos as $equipamento) {
            // Verificar se já existe um equipamento com a mesma tag
            $existing = EquipamentoTest::where('tag', $equipamento['tag'])->first();
            
            if (!$existing) {
                EquipamentoTest::create($equipamento);
                $this->command->info("Equipamento {$equipamento['tag']} criado.");
            } else {
                $this->command->info("Equipamento {$equipamento['tag']} já existe, pulando...");
            }
        }

        $this->command->info('Seeder de equipamentos de teste concluído!');
        
        // Mostrar estatísticas
        $setores = EquipamentoTest::select('setor')
            ->whereNotNull('setor')
            ->where('setor', '!=', '')
            ->distinct()
            ->orderBy('setor')
            ->pluck('setor');

        $this->command->info("\nSetores disponíveis:");
        foreach ($setores as $setor) {
            $count = EquipamentoTest::where('setor', $setor)->count();
            $this->command->info("- {$setor}: {$count} equipamentos");
        }
    }
} 