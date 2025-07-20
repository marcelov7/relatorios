<?php
/**
 * Script para verificar o status das migrações e identificar problemas
 */

// Configurações do banco de dados - ALTERE ESTAS INFORMAÇÕES
$host = 'localhost';
$dbname = 'relatodb'; // Nome do seu banco
$username = 'seu_usuario'; // ALTERE PARA SEU USUÁRIO
$password = 'sua_senha'; // ALTERE PARA SUA SENHA

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco de dados com sucesso!\n\n";
    
    // 1. Verificar se a tabela migrations existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'migrations' não existe!\n";
        exit(1);
    }
    echo "✅ Tabela 'migrations' existe.\n\n";
    
    // 2. Verificar migrações relacionadas a motores
    echo "📋 Migrações relacionadas a motores:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("SELECT * FROM migrations WHERE migration LIKE '%motores%' ORDER BY batch DESC, migration ASC");
    $motoresMigrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($motoresMigrations)) {
        echo "⚠️  Nenhuma migração relacionada a motores encontrada.\n";
    } else {
        foreach ($motoresMigrations as $migration) {
            echo sprintf("%-50s | Batch: %-5s\n", 
                $migration['migration'], 
                $migration['batch']
            );
        }
    }
    
    // 3. Verificar se há migrações pendentes
    echo "\n🔍 Verificando migrações pendentes...\n";
    
    // Lista de migrações que deveriam existir
    $expectedMigrations = [
        '2024_01_01_000000_create_motores_table',
        '2025_07_17_204222_update_motores_table_structure',
        '2025_07_17_231730_fix_motores_table_structure',
        '2025_07_17_232924_fix_motores_table_complete'
    ];
    
    $executedMigrations = array_column($motoresMigrations, 'migration');
    
    foreach ($expectedMigrations as $expected) {
        if (in_array($expected, $executedMigrations)) {
            echo "✅ $expected - Executada\n";
        } else {
            echo "❌ $expected - NÃO executada\n";
        }
    }
    
    // 4. Verificar estrutura atual da tabela motores
    echo "\n📋 Estrutura atual da tabela motores:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("DESCRIBE motores");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-25s %-20s %-8s %-8s %-12s %-8s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'], 
            $column['Extra']
        );
    }
    
    // 5. Verificar se o campo tipo_equipamento_modelo existe
    echo "\n🔍 Verificando campo tipo_equipamento_modelo:\n";
    
    $tipoEquipamentoExists = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'tipo_equipamento_modelo') {
            $tipoEquipamentoExists = true;
            echo "✅ Campo 'tipo_equipamento_modelo' existe.\n";
            echo "   Tipo: " . $column['Type'] . "\n";
            echo "   Null: " . $column['Null'] . "\n";
            break;
        }
    }
    
    if (!$tipoEquipamentoExists) {
        echo "❌ Campo 'tipo_equipamento_modelo' NÃO existe!\n";
        
        // Verificar se existe como tipo_equipamento
        foreach ($columns as $column) {
            if ($column['Field'] === 'tipo_equipamento') {
                echo "⚠️  Campo 'tipo_equipamento' existe. Pode ser necessário renomear.\n";
                break;
            }
        }
    }
    
    // 6. Sugestões de correção
    echo "\n💡 Sugestões de correção:\n";
    
    if (!$tipoEquipamentoExists) {
        echo "1. Execute: php artisan migrate:status\n";
        echo "2. Execute: php artisan migrate\n";
        echo "3. Se houver erro, execute: php artisan migrate:rollback --step=1\n";
        echo "4. Depois execute: php artisan migrate novamente\n";
    }
    
    // 7. Verificar logs de erro
    echo "\n📋 Verificando logs de erro...\n";
    $logFile = __DIR__ . '/storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $lastLines = file($logFile);
        $recentErrors = array_slice($lastLines, -10); // Últimas 10 linhas
        
        echo "Últimas linhas do log:\n";
        foreach ($recentErrors as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
                echo "🔴 " . trim($line) . "\n";
            }
        }
    } else {
        echo "ℹ️  Arquivo de log não encontrado.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 