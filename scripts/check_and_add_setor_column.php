<?php
/**
 * Script para verificar e adicionar a coluna 'setor' na tabela equipamento_tests
 * Execute este script no CloudPanel para garantir que a coluna existe
 */

// Configurações do banco (ajuste conforme necessário)
$host = 'localhost';
$dbname = 'sistema_relatorios'; // Ajuste para o nome do seu banco
$username = 'root'; // Ajuste para o usuário do CloudPanel
$password = ''; // Ajuste para a senha do CloudPanel

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco de dados: $dbname\n\n";
    
    // 1. Verificar se a tabela equipamento_tests existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'equipamento_tests'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'equipamento_tests' não existe!\n";
        echo "Criando tabela...\n";
        
        $sql = "CREATE TABLE equipamento_tests (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tag VARCHAR(255) UNIQUE NOT NULL,
            nome VARCHAR(255) NOT NULL,
            setor VARCHAR(255) NULL,
            status VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✅ Tabela 'equipamento_tests' criada com sucesso!\n\n";
    } else {
        echo "✅ Tabela 'equipamento_tests' existe\n";
    }
    
    // 2. Verificar se a coluna 'setor' existe
    $stmt = $pdo->query("SHOW COLUMNS FROM equipamento_tests LIKE 'setor'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Coluna 'setor' não existe na tabela equipamento_tests!\n";
        echo "Adicionando coluna...\n";
        
        $sql = "ALTER TABLE equipamento_tests ADD COLUMN setor VARCHAR(255) NULL AFTER nome";
        $pdo->exec($sql);
        echo "✅ Coluna 'setor' adicionada com sucesso!\n\n";
    } else {
        echo "✅ Coluna 'setor' já existe na tabela equipamento_tests\n";
    }
    
    // 3. Verificar estrutura atual da tabela
    echo "\n📋 Estrutura atual da tabela equipamento_tests:\n";
    $stmt = $pdo->query("DESCRIBE equipamento_tests");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']} " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
    
    // 4. Verificar se há dados na tabela
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM equipamento_tests");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($total == 0) {
        echo "\n📝 Tabela está vazia. Inserindo dados de exemplo...\n";
        
        $equipamentos = [
            ['EQPROD001', 'Máquina de Corte', 'Produção', 'Operacional'],
            ['EQPROD002', 'Esteira Transportadora', 'Produção', 'Operacional'],
            ['EQMAN001', 'Compressor de Ar', 'Manutenção', 'Operacional'],
            ['EQMAN002', 'Gerador de Energia', 'Manutenção', 'Operacional'],
            ['EQQUAL001', 'Microscópio Digital', 'Qualidade', 'Operacional'],
            ['EQQUAL002', 'Balança de Precisão', 'Qualidade', 'Operacional'],
            ['EQLOG001', 'Empilhadeira', 'Logística', 'Operacional'],
            ['EQLAB001', 'Centrífuga', 'Laboratório', 'Operacional'],
        ];
        
        $sql = "INSERT INTO equipamento_tests (tag, nome, setor, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        
        foreach ($equipamentos as $equip) {
            try {
                $stmt->execute($equip);
                echo "✅ Inserido: {$equip[0]} - {$equip[1]} ({$equip[2]})\n";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry
                    echo "⚠️  Já existe: {$equip[0]} - {$equip[1]}\n";
                } else {
                    echo "❌ Erro ao inserir {$equip[0]}: " . $e->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "\n📊 Tabela possui $total registros\n";
    }
    
    // 5. Mostrar setores únicos disponíveis
    echo "\n🏭 Setores únicos disponíveis:\n";
    $stmt = $pdo->query("
        SELECT 
            setor,
            COUNT(*) as total_equipamentos
        FROM equipamento_tests 
        WHERE setor IS NOT NULL 
        AND setor != ''
        GROUP BY setor
        ORDER BY setor
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['setor']}: {$row['total_equipamentos']} equipamentos\n";
    }
    
    // 6. Testar filtro de relatórios por setor
    echo "\n🔍 Testando filtro de relatórios por setor:\n";
    $stmt = $pdo->query("
        SELECT 
            et.setor,
            COUNT(r.id) as total_relatorios
        FROM equipamento_tests et
        LEFT JOIN relatorio_equipamento_test ret ON et.id = ret.equipamento_test_id
        LEFT JOIN relatorios r ON ret.relatorio_id = r.id
        WHERE et.setor IS NOT NULL 
        AND et.setor != ''
        GROUP BY et.setor
        ORDER BY et.setor
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- Setor '{$row['setor']}': {$row['total_relatorios']} relatórios\n";
    }
    
    echo "\n🎉 Script executado com sucesso!\n";
    echo "O filtro por setor deve estar funcionando na página de relatórios.\n";
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique:\n";
    echo "1. Credenciais do banco de dados\n";
    echo "2. Nome do banco de dados\n";
    echo "3. Permissões do usuário\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 