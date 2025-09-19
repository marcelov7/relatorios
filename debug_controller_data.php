<?php

require_once 'vendor/autoload.php';

use App\Models\InspecaoGerador;
use App\Models\User;
use App\Models\Setor;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMULAR CONTROLLER SHOW ===\n";

$inspecaoGerador = InspecaoGerador::find(1);
if ($inspecaoGerador) {
    $inspecaoGerador->load(['user', 'colaborador', 'setor']);

    echo "Dados que seriam enviados para o frontend:\n";
    echo json_encode($inspecaoGerador->toArray(), JSON_PRETTY_PRINT) . "\n";
    
    echo "\n=== VERIFICAR CAMPOS ESPECÍFICOS ===\n";
    echo "data_inspecao: " . var_export($inspecaoGerador->data_inspecao, true) . "\n";
    echo "colaborador_id: " . var_export($inspecaoGerador->colaborador_id, true) . "\n";
    echo "colaborador->name: " . var_export($inspecaoGerador->colaborador ? $inspecaoGerador->colaborador->name : null, true) . "\n";
    echo "setor_id: " . var_export($inspecaoGerador->setor_id, true) . "\n";
    echo "setor->nome: " . var_export($inspecaoGerador->setor ? $inspecaoGerador->setor->nome : null, true) . "\n";
    echo "setor_text: " . var_export($inspecaoGerador->setor_text, true) . "\n";
    echo "tensao_a: " . var_export($inspecaoGerador->tensao_a, true) . "\n";
    echo "frequencia: " . var_export($inspecaoGerador->frequencia, true) . "\n";
    echo "pressao_oleo: " . var_export($inspecaoGerador->pressao_oleo, true) . "\n";
    echo "temperatura_agua: " . var_export($inspecaoGerador->temperatura_agua, true) . "\n";
    echo "nivel_combustivel: " . var_export($inspecaoGerador->nivel_combustivel, true) . "\n";
} else {
    echo "Inspeção não encontrada!\n";
} 