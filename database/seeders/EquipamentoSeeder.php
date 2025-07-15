<?php

namespace Database\Seeders;

use App\Models\Equipamento;
use App\Models\Local;
use Illuminate\Database\Seeder;

class EquipamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipamentos = [
            // Galpão de Produção A
            [
                'equipment_tag' => 'PROD-A-001',
                'nome' => 'Torno CNC Principal',
                'descricao' => 'Torno CNC de alta precisão para usinagem de peças',
                'local_id' => 2, // Galpão de Produção A
                'tipo' => 'Máquina CNC',
                'marca' => 'Haas',
                'modelo' => 'ST-30',
                'numero_serie' => 'HST30-2023-001',
                'data_instalacao' => '2023-01-15',
                'data_ultima_manutencao' => '2024-06-15',
                'proxima_manutencao' => '2024-12-15',
                'status' => 'Operacional',
                'observacoes' => 'Equipamento em perfeito estado',
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'PROD-A-002',
                'nome' => 'Fresadora Universal',
                'descricao' => 'Fresadora universal para operações diversas',
                'local_id' => 2,
                'tipo' => 'Fresadora',
                'marca' => 'Romi',
                'modelo' => 'U-30',
                'numero_serie' => 'RU30-2022-005',
                'data_instalacao' => '2022-03-10',
                'data_ultima_manutencao' => '2024-05-20',
                'proxima_manutencao' => '2024-11-20',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'PROD-A-003',
                'nome' => 'Compressor de Ar',
                'descricao' => 'Compressor de ar para alimentação pneumática',
                'local_id' => 2,
                'tipo' => 'Compressor',
                'marca' => 'Schulz',
                'modelo' => 'MSV-60',
                'numero_serie' => 'SCH60-2021-012',
                'data_instalacao' => '2021-08-05',
                'data_ultima_manutencao' => '2024-07-01',
                'proxima_manutencao' => '2025-01-01',
                'status' => 'Operacional',
                'observacoes' => 'Manutenção preventiva em dia',
                'ativo' => true,
            ],
            
            // Galpão de Produção B
            [
                'equipment_tag' => 'PROD-B-001',
                'nome' => 'Prensa Hidráulica',
                'descricao' => 'Prensa hidráulica de 100 toneladas',
                'local_id' => 3, // Galpão de Produção B
                'tipo' => 'Prensa',
                'marca' => 'Vamaq',
                'modelo' => 'PH-100',
                'numero_serie' => 'VAM100-2023-003',
                'data_instalacao' => '2023-04-20',
                'data_ultima_manutencao' => '2024-04-20',
                'proxima_manutencao' => '2024-10-20',
                'status' => 'Manutenção',
                'observacoes' => 'Aguardando peças de reposição',
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'PROD-B-002',
                'nome' => 'Solda MIG/MAG',
                'descricao' => 'Máquina de solda MIG/MAG industrial',
                'local_id' => 3,
                'tipo' => 'Solda',
                'marca' => 'Esab',
                'modelo' => 'LAI-418',
                'numero_serie' => 'ESB418-2022-008',
                'data_instalacao' => '2022-11-12',
                'data_ultima_manutencao' => '2024-06-10',
                'proxima_manutencao' => '2024-12-10',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            
            // Estoque Central
            [
                'equipment_tag' => 'EST-001',
                'nome' => 'Empilhadeira Elétrica',
                'descricao' => 'Empilhadeira elétrica para movimentação de materiais',
                'local_id' => 4, // Estoque Central
                'tipo' => 'Empilhadeira',
                'marca' => 'Hyster',
                'modelo' => 'E2.0XN',
                'numero_serie' => 'HYS20-2023-007',
                'data_instalacao' => '2023-02-28',
                'data_ultima_manutencao' => '2024-07-15',
                'proxima_manutencao' => '2025-01-15',
                'status' => 'Operacional',
                'observacoes' => 'Bateria substituída recentemente',
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'EST-002',
                'nome' => 'Transpaleteira Elétrica',
                'descricao' => 'Transpaleteira elétrica para cargas leves',
                'local_id' => 4,
                'tipo' => 'Transpaleteira',
                'marca' => 'Crown',
                'modelo' => 'PE-4000',
                'numero_serie' => 'CRW4000-2022-015',
                'data_instalacao' => '2022-09-14',
                'data_ultima_manutencao' => '2024-05-30',
                'proxima_manutencao' => '2024-11-30',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            
            // Laboratório de Qualidade
            [
                'equipment_tag' => 'LAB-001',
                'nome' => 'Microscópio Digital',
                'descricao' => 'Microscópio digital para análise de materiais',
                'local_id' => 5, // Laboratório de Qualidade
                'tipo' => 'Microscópio',
                'marca' => 'Olympus',
                'modelo' => 'DSX1000',
                'numero_serie' => 'OLY1000-2023-001',
                'data_instalacao' => '2023-06-01',
                'data_ultima_manutencao' => '2024-06-01',
                'proxima_manutencao' => '2025-06-01',
                'status' => 'Operacional',
                'observacoes' => 'Calibração anual em dia',
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'LAB-002',
                'nome' => 'Balança de Precisão',
                'descricao' => 'Balança analítica de alta precisão',
                'local_id' => 5,
                'tipo' => 'Balança',
                'marca' => 'Mettler Toledo',
                'modelo' => 'XPR205',
                'numero_serie' => 'MT205-2022-003',
                'data_instalacao' => '2022-12-10',
                'data_ultima_manutencao' => '2024-06-10',
                'proxima_manutencao' => '2024-12-10',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            
            // Sala de Servidores
            [
                'equipment_tag' => 'TI-001',
                'nome' => 'Servidor Principal',
                'descricao' => 'Servidor Dell PowerEdge para aplicações críticas',
                'local_id' => 7, // Sala de Servidores
                'tipo' => 'Servidor',
                'marca' => 'Dell',
                'modelo' => 'PowerEdge R750',
                'numero_serie' => 'DELL750-2023-001',
                'data_instalacao' => '2023-01-10',
                'data_ultima_manutencao' => '2024-07-01',
                'proxima_manutencao' => '2025-01-01',
                'status' => 'Operacional',
                'observacoes' => 'Monitoramento 24/7 ativo',
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'TI-002',
                'nome' => 'Switch Principal',
                'descricao' => 'Switch Cisco 48 portas para rede principal',
                'local_id' => 7,
                'tipo' => 'Switch',
                'marca' => 'Cisco',
                'modelo' => 'Catalyst 2960-X',
                'numero_serie' => 'CSC2960-2022-005',
                'data_instalacao' => '2022-05-15',
                'data_ultima_manutencao' => '2024-05-15',
                'proxima_manutencao' => '2025-05-15',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            
            // Oficina de Manutenção
            [
                'equipment_tag' => 'MAN-001',
                'nome' => 'Furadeira de Bancada',
                'descricao' => 'Furadeira de bancada para reparos diversos',
                'local_id' => 6, // Oficina de Manutenção
                'tipo' => 'Furadeira',
                'marca' => 'Bosch',
                'modelo' => 'GBM-32',
                'numero_serie' => 'BSH32-2021-010',
                'data_instalacao' => '2021-10-20',
                'data_ultima_manutencao' => '2024-04-15',
                'proxima_manutencao' => '2024-10-15',
                'status' => 'Operacional',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'equipment_tag' => 'MAN-002',
                'nome' => 'Esmerilhadeira Industrial',
                'descricao' => 'Esmerilhadeira de bancada para acabamento',
                'local_id' => 6,
                'tipo' => 'Esmerilhadeira',
                'marca' => 'Makita',
                'modelo' => 'GB801',
                'numero_serie' => 'MAK801-2022-007',
                'data_instalacao' => '2022-07-08',
                'data_ultima_manutencao' => '2024-03-20',
                'proxima_manutencao' => '2024-09-20',
                'status' => 'Defeito',
                'observacoes' => 'Motor queimado, aguardando reparo',
                'ativo' => true,
            ],
        ];

        foreach ($equipamentos as $equipamento) {
            Equipamento::create($equipamento);
        }
    }
}
