<?php
/**
 * Script para corrigir o campo local da tabela motores
 * Remover chaves e alterar para VARCHAR (não TEXT para evitar problemas)
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
    
    // 1. Verificar o tipo atual da coluna local
    $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'local'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📋 Tipo atual da coluna 'local': " . $column['Type'] . "\n";
    echo "📋 Chave: " . $column['Key'] . "\n";
    echo "📋 Null: " . $column['Null'] . "\n";
    
    // 2. Verificar se há dados na coluna
    $stmt = $pdo->query("SELECT DISTINCT local FROM motores WHERE local IS NOT NULL LIMIT 5");
    $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($values)) {
        echo "\n⚠️  Valores encontrados na coluna local:\n";
        foreach ($values as $value) {
            echo "   - '$value'\n";
        }
        echo "\n💡 Estes valores serão preservados durante a conversão.\n\n";
    } else {
        echo "\nℹ️  Coluna local está vazia ou com NULL.\n\n";
    }
    
    // 3. Verificar TODOS os índices da tabela
    echo "🔍 Verificando todos os índices da tabela motores...\n";
    $stmt = $pdo->query("SHOW INDEX FROM motores");
    $allIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $localIndexes = [];
    foreach ($allIndexes as $index) {
        if ($index['Column_name'] === 'local') {
            $localIndexes[] = $index;
        }
    }
    
    if (!empty($localIndexes)) {
        echo "⚠️  Índices encontrados na coluna local:\n";
        foreach ($localIndexes as $index) {
            echo "   - Nome: " . $index['Key_name'] . " | Tipo: " . $index['Index_type'] . "\n";
        }
        echo "\n💡 Estes índices serão removidos.\n\n";
    } else {
        echo "ℹ️  Nenhum índice encontrado na coluna local.\n\n";
    }
    
    // 4. Remover índices da coluna local
    echo "🔄 Removendo índices da coluna local...\n";
    
    if (!empty($localIndexes)) {
        foreach ($localIndexes as $index) {
            $indexName = $index['Key_name'];
            if ($indexName !== 'PRIMARY') { // Não remover chave primária
                try {
                    $sql = "ALTER TABLE motores DROP INDEX `$indexName`";
                    $pdo->exec($sql);
                    echo "✅ Índice '$indexName' removido.\n";
                } catch (PDOException $e) {
                    echo "⚠️  Erro ao remover índice '$indexName': " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // 5. Alterar o tipo da coluna para VARCHAR (mais seguro que TEXT)
    echo "\n🔄 Alterando tipo da coluna local para VARCHAR...\n";
    
    try {
        // Usar VARCHAR em vez de TEXT para evitar problemas com chaves
        $sql = "ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL";
        $pdo->exec($sql);
        
        echo "✅ Coluna 'local' alterada para VARCHAR(255) com sucesso!\n\n";
        
    } catch (PDOException $e) {
        echo "❌ Erro ao alterar coluna: " . $e->getMessage() . "\n";
        
        // Tentar abordagem alternativa
        echo "\n🔄 Tentando abordagem alternativa...\n";
        
        try {
            // Adicionar nova coluna
            $pdo->exec("ALTER TABLE motores ADD COLUMN local_new VARCHAR(255) NULL AFTER local");
            echo "✅ Nova coluna 'local_new' criada.\n";
            
            // Copiar dados
            $pdo->exec("UPDATE motores SET local_new = CAST(local AS CHAR) WHERE local IS NOT NULL");
            echo "✅ Dados copiados para nova coluna.\n";
            
            // Remover coluna antiga
            $pdo->exec("ALTER TABLE motores DROP COLUMN local");
            echo "✅ Coluna antiga removida.\n";
            
            // Renomear nova coluna
            $pdo->exec("ALTER TABLE motores CHANGE local_new local VARCHAR(255) NULL");
            echo "✅ Nova coluna renomeada para 'local'.\n";
            
        } catch (PDOException $e2) {
            echo "❌ Erro na abordagem alternativa: " . $e2->getMessage() . "\n";
            exit(1);
        }
    }
    
    // 6. Verificar se a alteração foi feita
    $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'local'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\n📋 Novo tipo da coluna 'local': " . $column['Type'] . "\n";
    echo "📋 Chave: " . $column['Key'] . "\n";
    echo "📋 Null: " . $column['Null'] . "\n";
    
    // 7. Testar inserção
    echo "\n🧪 Testando inserção de dados...\n";
    
    try {
        $testValue = 'Local Teste';
        $sql = "INSERT INTO motores (tag, equipamento, local, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['TESTE_LOCAL_' . time(), 'Equipamento Teste', $testValue]);
        
        echo "✅ Inserção com valor '$testValue' funcionou!\n";
        
        // Verificar se foi inserido
        $lastId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT local FROM motores WHERE id = ?");
        $stmt->execute([$lastId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Valor recuperado: '" . $result['local'] . "'\n";
        
        // Limpar teste
        $pdo->exec("DELETE FROM motores WHERE id = $lastId");
        echo "✅ Registro de teste removido.\n";
        
    } catch (PDOException $e) {
        echo "❌ Erro na inserção: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Correção do campo 'local' concluída com sucesso!\n";
    echo "💡 O campo agora é VARCHAR(255) sem chaves, permitindo texto livre.\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 