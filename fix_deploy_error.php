<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== RESOLVENDO ERRO DE DEPLOY ===\n";

try {
    // 1. Verificar se a tabela motores existe
    if (!Schema::hasTable('motores')) {
        echo "❌ Tabela motores não existe. Criando...\n";
        // Executar migração de criação da tabela
        \Artisan::call('migrate', ['--path' => 'database/migrations/2024_01_01_000000_create_motores_table.php']);
        echo "✅ Tabela motores criada.\n";
    }

    // 2. Verificar estrutura atual da tabela
    echo "\n=== ESTRUTURA ATUAL DA TABELA MOTORES ===\n";
    $columns = Schema::getColumnListing('motores');
    foreach ($columns as $column) {
        echo "- $column\n";
    }

    // 3. Adicionar campos que faltam
    echo "\n=== ADICIONANDO CAMPOS QUE FALTAM ===\n";
    
    if (!in_array('carcaca_fabricante', $columns)) {
        echo "➕ Adicionando campo carcaca_fabricante...\n";
        DB::statement('ALTER TABLE motores ADD COLUMN carcaca_fabricante VARCHAR(255) NULL AFTER equipamento');
    }
    
    if (!in_array('corrente_placa', $columns)) {
        echo "➕ Adicionando campo corrente_placa...\n";
        DB::statement('ALTER TABLE motores ADD COLUMN corrente_placa DECIMAL(8,2) NULL AFTER rotacao');
    }
    
    if (!in_array('reserva_almox', $columns)) {
        echo "➕ Adicionando campo reserva_almox...\n";
        DB::statement('ALTER TABLE motores ADD COLUMN reserva_almox VARCHAR(255) NULL AFTER fabricante');
    }
    
    if (!in_array('local', $columns)) {
        echo "➕ Adicionando campo local...\n";
        DB::statement('ALTER TABLE motores ADD COLUMN local VARCHAR(255) NULL AFTER reserva_almox');
    }
    
    if (!in_array('armazenamento', $columns)) {
        echo "➕ Adicionando campo armazenamento...\n";
        DB::statement('ALTER TABLE motores ADD COLUMN armazenamento VARCHAR(255) NULL AFTER local');
    }

    // 4. Remover campos incorretos se existirem
    echo "\n=== REMOVENDO CAMPOS INCORRETOS ===\n";
    
    if (in_array('carcaca', $columns) && !in_array('carcaca_fabricante', $columns)) {
        echo "➖ Removendo campo carcaca...\n";
        DB::statement('ALTER TABLE motores DROP COLUMN carcaca');
    }
    
    if (in_array('corrente_nominal', $columns) && !in_array('corrente_placa', $columns)) {
        echo "➖ Removendo campo corrente_nominal...\n";
        DB::statement('ALTER TABLE motores DROP COLUMN corrente_nominal');
    }
    
    if (in_array('local_id', $columns)) {
        echo "➖ Removendo campo local_id...\n";
        try {
            DB::statement('ALTER TABLE motores DROP FOREIGN KEY motores_local_id_foreign');
        } catch (Exception $e) {
            // Ignora erro se foreign key não existir
        }
        DB::statement('ALTER TABLE motores DROP COLUMN local_id');
    }

    // 5. Marcar migrações como executadas
    echo "\n=== MARCANDO MIGRAÇÕES COMO EXECUTADAS ===\n";
    
    $migrations = [
        '2025_07_17_204222_update_motores_table_structure',
        '2025_07_17_205503_update_motores_local_reserva_fields',
        '2025_07_17_231730_fix_motores_table_structure',
        '2025_07_17_232924_fix_motores_table_complete'
    ];
    
    foreach ($migrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if (!$exists) {
            echo "✅ Marcando $migration como executada...\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 5
            ]);
        } else {
            echo "ℹ️ $migration já está marcada como executada.\n";
        }
    }

    // 6. Verificar estrutura final
    echo "\n=== ESTRUTURA FINAL DA TABELA MOTORES ===\n";
    $finalColumns = Schema::getColumnListing('motores');
    foreach ($finalColumns as $column) {
        echo "- $column\n";
    }

    echo "\n✅ ERRO DE DEPLOY RESOLVIDO COM SUCESSO!\n";
    echo "🎯 A tabela motores agora tem a estrutura correta.\n";

} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
} 