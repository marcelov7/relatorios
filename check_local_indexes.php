<?php
/**
 * Script para verificar exatamente quais índices existem na coluna local
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
    echo "🔍 TODOS os índices da tabela motores:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("SHOW INDEX FROM motores");
    $allIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($allIndexes)) {
        echo "ℹ️  Nenhum índice encontrado na tabela motores.\n";
    } else {
        echo sprintf("%-20s | %-15s | %-15s | %-10s | %-10s\n", 
            'Nome do Índice', 'Coluna', 'Tipo', 'Único', 'Sequência'
        );
        echo str_repeat("-", 80) . "\n";
        
        foreach ($allIndexes as $index) {
            echo sprintf("%-20s | %-15s | %-15s | %-10s | %-10s\n", 
                $index['Key_name'], 
                $index['Column_name'], 
                $index['Index_type'], 
                $index['Non_unique'] ? 'Não' : 'Sim',
                $index['Seq_in_index']
            );
        }
    }
    
    // 2. Verificar especificamente índices da coluna local
    echo "\n🔍 Índices da coluna 'local':\n";
    echo str_repeat("-", 50) . "\n";
    
    $localIndexes = [];
    foreach ($allIndexes as $index) {
        if ($index['Column_name'] === 'local') {
            $localIndexes[] = $index;
        }
    }
    
    if (empty($localIndexes)) {
        echo "✅ Nenhum índice encontrado na coluna 'local'!\n";
        echo "💡 Você pode alterar o tipo da coluna diretamente:\n";
        echo "   ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;\n";
    } else {
        echo "⚠️  Índices encontrados na coluna 'local':\n";
        foreach ($localIndexes as $index) {
            echo "   - Nome: '" . $index['Key_name'] . "' | Tipo: " . $index['Index_type'] . "\n";
        }
        
        echo "\n💡 Comandos para remover estes índices:\n";
        foreach ($localIndexes as $index) {
            $indexName = $index['Key_name'];
            if ($indexName !== 'PRIMARY') {
                echo "   ALTER TABLE motores DROP INDEX `$indexName`;\n";
            } else {
                echo "   ⚠️  '$indexName' é a chave primária - NÃO REMOVER!\n";
            }
        }
    }
    
    // 3. Verificar estrutura da coluna local
    echo "\n📋 Estrutura da coluna 'local':\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'local'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "   Campo: " . $column['Field'] . "\n";
        echo "   Tipo: " . $column['Type'] . "\n";
        echo "   Null: " . $column['Null'] . "\n";
        echo "   Chave: " . $column['Key'] . "\n";
        echo "   Default: " . ($column['Default'] ?? 'NULL') . "\n";
        echo "   Extra: " . $column['Extra'] . "\n";
    } else {
        echo "❌ Coluna 'local' não encontrada!\n";
    }
    
    // 4. Sugestões baseadas no resultado
    echo "\n💡 Sugestões:\n";
    
    if (empty($localIndexes)) {
        echo "✅ Não há índices na coluna 'local'. Você pode alterar o tipo diretamente.\n";
    } else {
        $hasNonPrimaryIndexes = false;
        foreach ($localIndexes as $index) {
            if ($index['Key_name'] !== 'PRIMARY') {
                $hasNonPrimaryIndexes = true;
                break;
            }
        }
        
        if ($hasNonPrimaryIndexes) {
            echo "⚠️  Remova os índices listados acima antes de alterar o tipo da coluna.\n";
        } else {
            echo "✅ Apenas chave primária na coluna 'local'. Você pode alterar o tipo diretamente.\n";
        }
    }
    
    echo "\n🎉 Verificação concluída!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 