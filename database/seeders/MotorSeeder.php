<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Motor;
use App\Models\Local;

class MotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter locais existentes
        $locais = Local::all();
        
        if ($locais->isEmpty()) {
            $this->command->warn('Nenhum local encontrado. Execute o LocalSeeder primeiro.');
            return;
        }

        $motores = [
            [
                'tag' => 'MOT-001',
                'equipamento' => 'Motor Bomba Principal',
                'carcaca' => 'WEG',
                'potencia_kw' => 75.0,
                'potencia_cv' => 102.0,
                'rotacao' => 1750,
                'corrente_placa' => 125.0,
                'corrente_configurada' => 120.0,
                'tipo_equipamento' => 'Motor Trifásico',
                'fabricante' => 'WEG',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Motor principal do sistema de bombeamento',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-002',
                'equipamento' => 'Motor Ventilador',
                'carcaca' => 'Siemens',
                'potencia_kw' => 15.0,
                'potencia_cv' => 20.4,
                'rotacao' => 3450,
                'corrente_placa' => 28.0,
                'corrente_configurada' => 25.0,
                'tipo_equipamento' => 'Motor Ventilador',
                'fabricante' => 'Siemens',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Ventilador de exaustão',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-003',
                'equipamento' => 'Motor Compressor',
                'carcaca' => 'ABB',
                'potencia_kw' => 55.0,
                'potencia_cv' => 74.8,
                'rotacao' => 1750,
                'corrente_placa' => 95.0,
                'corrente_configurada' => 90.0,
                'tipo_equipamento' => 'Motor Compressor',
                'fabricante' => 'ABB',
                'reserva_almox' => true,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Almoxarifado',
                'observacoes' => 'Motor reserva para compressor',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-004',
                'equipamento' => 'Motor Esteira',
                'carcaca' => 'Toshiba',
                'potencia_kw' => 7.5,
                'potencia_cv' => 10.2,
                'rotacao' => 1750,
                'corrente_placa' => 15.0,
                'corrente_configurada' => 14.0,
                'tipo_equipamento' => 'Motor Esteira',
                'fabricante' => 'Toshiba',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Motor da esteira transportadora',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-005',
                'equipamento' => 'Motor Britador',
                'carcaca' => 'WEG',
                'potencia_kw' => 200.0,
                'potencia_cv' => 272.0,
                'rotacao' => 1200,
                'corrente_placa' => 350.0,
                'corrente_configurada' => 340.0,
                'tipo_equipamento' => 'Motor Britador',
                'fabricante' => 'WEG',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Motor principal do britador',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-006',
                'equipamento' => 'Motor Peneira',
                'carcaca' => 'Siemens',
                'potencia_kw' => 5.5,
                'potencia_cv' => 7.5,
                'rotacao' => 3450,
                'corrente_placa' => 10.5,
                'corrente_configurada' => 10.0,
                'tipo_equipamento' => 'Motor Peneira',
                'fabricante' => 'Siemens',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Manutenção',
                'observacoes' => 'Motor em manutenção preventiva',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-007',
                'equipamento' => 'Motor Moinho',
                'carcaca' => 'ABB',
                'potencia_kw' => 150.0,
                'potencia_cv' => 204.0,
                'rotacao' => 1500,
                'corrente_placa' => 260.0,
                'corrente_configurada' => 250.0,
                'tipo_equipamento' => 'Motor Moinho',
                'fabricante' => 'ABB',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Motor do moinho de bolas',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-008',
                'equipamento' => 'Motor Elevador',
                'carcaca' => 'WEG',
                'potencia_kw' => 22.0,
                'potencia_cv' => 30.0,
                'rotacao' => 1750,
                'corrente_placa' => 40.0,
                'corrente_configurada' => 38.0,
                'tipo_equipamento' => 'Motor Elevador',
                'fabricante' => 'WEG',
                'reserva_almox' => true,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Almoxarifado',
                'observacoes' => 'Motor reserva para elevador de canecas',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-009',
                'equipamento' => 'Motor Secador',
                'carcaca' => 'Toshiba',
                'potencia_kw' => 45.0,
                'potencia_cv' => 61.2,
                'rotacao' => 1750,
                'corrente_placa' => 80.0,
                'corrente_configurada' => 75.0,
                'tipo_equipamento' => 'Motor Secador',
                'fabricante' => 'Toshiba',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Instalado',
                'observacoes' => 'Motor do secador rotativo',
                'ativo' => true,
            ],
            [
                'tag' => 'MOT-010',
                'equipamento' => 'Motor Triturador',
                'carcaca' => 'Siemens',
                'potencia_kw' => 90.0,
                'potencia_cv' => 122.4,
                'rotacao' => 1200,
                'corrente_placa' => 160.0,
                'corrente_configurada' => 155.0,
                'tipo_equipamento' => 'Motor Triturador',
                'fabricante' => 'Siemens',
                'reserva_almox' => false,
                'local_id' => $locais->random()->id,
                'armazenamento' => 'Descartado',
                'observacoes' => 'Motor descartado por obsolescência',
                'ativo' => false,
            ],
        ];

        foreach ($motores as $motorData) {
            Motor::create($motorData);
        }

        $this->command->info('Motores criados com sucesso!');
    }
}
