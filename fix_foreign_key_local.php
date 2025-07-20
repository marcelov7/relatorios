<?php
/**
 * Script para identificar e remover foreign key constraints da coluna local
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
    
    // 1. Verificar foreign keys da tabela motores
    echo "🔍 Verificando foreign keys da tabela motores...\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = '$dbname' 
        AND TABLE_NAME = 'motores' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($foreignKeys)) {
        echo "ℹ️  Nenhuma foreign key encontrada na tabela motores.\n";
    } else {
        echo "📋 Foreign keys encontradas:\n";
        foreach ($foreignKeys as $fk) {
            echo sprintf("%-30s | %-15s | %-20s | %-20s\n", 
                $fk['CONSTRAINT_NAME'], 
                $fk['COLUMN_NAME'], 
                $fk['REFERENCED_TABLE_NAME'], 
                $fk['REFERENCED_COLUMN_NAME']
            );
        }
    }
    
    // 2. Verificar especificamente foreign keys da coluna local
    echo "\n🔍 Verificando foreign keys da coluna 'local'...\n";
    echo str_repeat("-", 50) . "\n";
    
    $localForeignKeys = [];
    foreach ($foreignKeys as $fk) {
        if ($fk['COLUMN_NAME'] === 'local') {
            $localForeignKeys[] = $fk;
        }
    }
    
    if (empty($localForeignKeys)) {
        echo "ℹ️  Nenhuma foreign key encontrada na coluna 'local'.\n";
    } else {
        echo "⚠️  Foreign keys encontradas na coluna 'local':\n";
        foreach ($localForeignKeys as $fk) {
            echo "   - Nome: " . $fk['CONSTRAINT_NAME'] . "\n";
            echo "     Referencia: " . $fk['REFERENCED_TABLE_NAME'] . "." . $fk['REFERENCED_COLUMN_NAME'] . "\n";
        }
        
        echo "\n💡 Comandos para remover estas foreign keys:\n";
        foreach ($localForeignKeys as $fk) {
            $constraintName = $fk['CONSTRAINT_NAME'];
            echo "   ALTER TABLE motores DROP FOREIGN KEY `$constraintName`;\n";
        }
    }
    
    // 3. Verificar índices da coluna local
    echo "\n🔍 Verificando índices da coluna 'local'...\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SHOW INDEX FROM motores WHERE Column_name = 'local'");
    $localIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($localIndexes)) {
        echo "ℹ️  Nenhum índice encontrado na coluna 'local'.\n";
    } else {
        echo "📋 Índices encontrados na coluna 'local':\n";
        foreach ($localIndexes as $index) {
            echo "   - Nome: " . $index['Key_name'] . " | Tipo: " . $index['Index_type'] . "\n";
        }
    }
    
    // 4. Sugestões de correção
    echo "\n💡 Sugestões de correção:\n";
    
    if (!empty($localForeignKeys)) {
        echo "1. Remova as foreign keys primeiro:\n";
        foreach ($localForeignKeys as $fk) {
            $constraintName = $fk['CONSTRAINT_NAME'];
            echo "   ALTER TABLE motores DROP FOREIGN KEY `$constraintName`;\n";
        }
        
        echo "\n2. Depois remova os índices:\n";
        foreach ($localIndexes as $index) {
            $indexName = $index['Key_name'];
            if ($indexName !== 'PRIMARY') {
                echo "   ALTER TABLE motores DROP INDEX `$indexName`;\n";
            }
        }
        
        echo "\n3. Finalmente altere o tipo da coluna:\n";
        echo "   ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;\n";
        
    } else {
        echo "✅ Não há foreign keys na coluna 'local'.\n";
        echo "💡 Você pode tentar remover os índices diretamente.\n";
    }
    
    // 5. Verificar se há dados na coluna local
    echo "\n🔍 Verificando dados na coluna 'local'...\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM motores WHERE local IS NOT NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   Registros com valor em 'local': " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "   ⚠️  Há dados na coluna 'local'. Eles serão preservados.\n";
    } else {
        echo "   ✅ Coluna 'local' está vazia.\n";
    }
    
    echo "\n🎉 Análise concluída!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 