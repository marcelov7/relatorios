<?php

namespace Database\Seeders;

use App\Models\EnderecoLogico;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class EnderecoLogicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('data/enderecos_logicos_base.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado em: {$csvPath}");
            return;
        }

        $tenantId = Tenant::first()?->id;

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Não foi possível abrir o arquivo CSV.");
            return;
        }

        $header = fgetcsv($handle, 0, ';'); // Lê cabeçalho TAGS;DESCRIÇÃO;ENDEREÇO
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty($row) || count($row) < 3) {
                continue;
            }

            $tag = trim($row[0] ?? '');
            $descricao = trim($row[1] ?? '');
            $endereco = trim($row[2] ?? '');

            if (empty($tag) && empty($endereco)) {
                continue;
            }

            // Detectar área aproximada a partir da tag ou endereço
            $area = null;
            if (str_starts_with($tag, 'K2C') || str_starts_with($tag, 'K1C')) {
                $area = 'Forno / Clinquer';
            } elseif (str_starts_with($tag, 'K2R') || str_starts_with($tag, 'K1R')) {
                $area = 'Resfriador';
            } elseif (str_starts_with($tag, 'K2P') || str_starts_with($tag, 'F2P') || str_starts_with($tag, 'F1P')) {
                $area = 'Filtro / Eletrofiltro';
            } elseif (str_starts_with($tag, 'K2T') || str_starts_with($tag, 'F2T') || str_starts_with($tag, 'H2T') || str_starts_with($tag, 'L2J')) {
                $area = 'Transporte / Elevação';
            } elseif (str_starts_with($tag, 'K2A') || str_starts_with($tag, 'F2R01A')) {
                $area = 'Torre / Análise de Gases';
            } elseif (str_starts_with($tag, 'S2Q') || str_starts_with($tag, 'S2J')) {
                $area = 'Resíduos / Moega';
            } elseif (str_starts_with($tag, 'H2H') || str_starts_with($tag, 'G2H')) {
                $area = 'Silos / Homogeneização';
            }

            EnderecoLogico::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'tag' => $tag,
                    'endereco' => $endereco,
                ],
                [
                    'descricao' => $descricao,
                    'area' => $area,
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->command->info("{$count} endereços lógicos semeados com sucesso!");
    }
}
