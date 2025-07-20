<?php
/**
 * Script para remover chaves/índices da coluna local
 * Execute este script ANTES de alterar o tipo da coluna
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
    
    // 1. Verificar TODOS os índices da tabela motores
    echo "🔍 Verificando TODOS os índices da tabela motores...\n";
    $stmt = $pdo->query("SHOW INDEX FROM motores");
    $allIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 Índices encontrados:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($allIndexes as $index) {
        echo sprintf("%-20s | %-15s | %-15s | %-10s\n", 
            $index['Key_name'], 
            $index['Column_name'], 
            $index['Index_type'], 
            $index['Non_unique']
        );
    }
    
    // 2. Identificar índices da coluna local
    $localIndexes = [];
    foreach ($allIndexes as $index) {
        if ($index['Column_name'] === 'local') {
            $localIndexes[] = $index;
        }
    }
    
    echo "\n🔍 Índices da coluna 'local':\n";
    if (!empty($localIndexes)) {
        foreach ($localIndexes as $index) {
            echo "   - Nome: " . $index['Key_name'] . " | Tipo: " . $index['Index_type'] . "\n";
        }
    } else {
        echo "   ℹ️  Nenhum índice encontrado na coluna 'local'.\n";
    }
    
    // 3. Remover índices da coluna local
    if (!empty($localIndexes)) {
        echo "\n🔄 Removendo índices da coluna 'local'...\n";
        
        foreach ($localIndexes as $index) {
            $indexName = $index['Key_name'];
            
            // Não remover chave primária
            if ($indexName === 'PRIMARY') {
                echo "⚠️  Pulando chave primária '$indexName'.\n";
                continue;
            }
            
            try {
                $sql = "ALTER TABLE motores DROP INDEX `$indexName`";
                $pdo->exec($sql);
                echo "✅ Índice '$indexName' removido com sucesso!\n";
            } catch (PDOException $e) {
                echo "❌ Erro ao remover índice '$indexName': " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "\nℹ️  Nenhum índice para remover da coluna 'local'.\n";
    }
    
    // 4. Verificar se ainda há índices na coluna local
    echo "\n🔍 Verificando se ainda há índices na coluna 'local'...\n";
    $stmt = $pdo->query("SHOW INDEX FROM motores WHERE Column_name = 'local'");
    $remainingIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($remainingIndexes)) {
        echo "✅ Todos os índices da coluna 'local' foram removidos!\n";
    } else {
        echo "⚠️  Ainda há índices na coluna 'local':\n";
        foreach ($remainingIndexes as $index) {
            echo "   - " . $index['Key_name'] . " (não foi possível remover)\n";
        }
    }
    
    // 5. Verificar estrutura atual da coluna
    echo "\n📋 Estrutura atual da coluna 'local':\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'local'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   Tipo: " . $column['Type'] . "\n";
    echo "   Chave: " . $column['Key'] . "\n";
    echo "   Null: " . $column['Null'] . "\n";
    
    // 6. Sugerir próximo passo
    echo "\n💡 Próximo passo:\n";
    if (empty($remainingIndexes) || (count($remainingIndexes) === 1 && $remainingIndexes[0]['Key_name'] === 'PRIMARY')) {
        echo "✅ Agora você pode alterar o tipo da coluna para VARCHAR:\n";
        echo "   ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;\n";
    } else {
        echo "⚠️  Ainda há índices na coluna. Remova-os manualmente antes de alterar o tipo.\n";
    }
    
    echo "\n🎉 Processo de remoção de índices concluído!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 