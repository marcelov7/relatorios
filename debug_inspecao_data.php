<?php

require_once 'vendor/autoload.php';

use App\Models\InspecaoGerador;
use App\Models\User;
use App\Models\Setor;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG INSPEÇÃO GERADOR ===\n";
echo "Total de inspeções: " . InspecaoGerador::count() . "\n\n";

if (InspecaoGerador::count() > 0) {
    $inspecao = InspecaoGerador::with(['user', 'colaborador', 'setor'])->first();
    
    echo "Primeira inspeção:\n";
    echo "ID: " . $inspecao->id . "\n";
    echo "Data inspeção: " . $inspecao->data_inspecao . "\n";
    echo "Colaborador ID: " . $inspecao->colaborador_id . "\n";
    echo "Colaborador Nome: " . ($inspecao->colaborador ? $inspecao->colaborador->name : 'null') . "\n";
    echo "Setor ID: " . $inspecao->setor_id . "\n";
    echo "Setor Nome: " . ($inspecao->setor ? $inspecao->setor->nome : 'null') . "\n";
    echo "Setor Text: " . $inspecao->setor_text . "\n";
    echo "Tensão A: " . $inspecao->tensao_a . "\n";
    echo "Frequência: " . $inspecao->frequencia . "\n";
    echo "Pressão óleo: " . $inspecao->pressao_oleo . "\n";
    echo "Temperatura água: " . $inspecao->temperatura_agua . "\n";
    echo "Nível combustível: " . $inspecao->nivel_combustivel . "\n";
    echo "Nível óleo motor parado: " . $inspecao->nivel_oleo_motor_parado . "\n";
    echo "Nível água parado: " . $inspecao->nivel_agua_parado . "\n";
    echo "Observações: " . $inspecao->observacoes . "\n";
    echo "Situação: " . $inspecao->situacao . "\n";
    
    echo "\n=== DADOS BRUTOS ===\n";
    echo json_encode($inspecao->toArray(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Nenhuma inspeção encontrada!\n";
}

echo "\n=== VERIFICAR TABELA ===\n";
try {
    $result = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='inspecao_geradores'");
    echo "Tabela inspecao_geradores existe: " . (count($result) > 0 ? 'SIM' : 'NÃO') . "\n";
    
    if (count($result) > 0) {
        $columns = \DB::select("PRAGMA table_info(inspecao_geradores)");
        echo "Colunas da tabela:\n";
        foreach ($columns as $column) {
            echo "- " . $column->name . " (" . $column->type . ")\n";
        }
    }
} catch (Exception $e) {
    echo "Erro ao verificar tabela: " . $e->getMessage() . "\n";
} 