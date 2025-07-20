<?php
/**
 * Script para debugar o problema com o campo tipo_equipamento_modelo
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
    
    // 1. Verificar se a tabela motores existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'motores'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Tabela 'motores' não existe!\n";
        exit(1);
    }
    echo "✅ Tabela 'motores' existe.\n\n";
    
    // 2. Verificar estrutura da tabela
    echo "📋 Estrutura da tabela motores:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->query("DESCRIBE motores");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-25s %-20s %-8s %-8s %-12s %-8s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key'], 
            $row['Default'], 
            $row['Extra']
        );
    }
    
    // 3. Verificar especificamente o campo tipo_equipamento_modelo
    echo "\n🔍 Verificando campo 'tipo_equipamento_modelo':\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'tipo_equipamento_modelo'");
    if ($stmt->rowCount() > 0) {
        $column = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Campo 'tipo_equipamento_modelo' existe.\n";
        echo "   Tipo: " . $column['Type'] . "\n";
        echo "   Null: " . $column['Null'] . "\n";
        echo "   Default: " . ($column['Default'] ?? 'NULL') . "\n";
        
        // 4. Testar inserção de dados
        echo "\n🧪 Testando inserção de dados...\n";
        
        try {
            // Testar inserção com valor simples
            $testValue = 'Teste Equipamento';
            $sql = "INSERT INTO motores (tag, equipamento, tipo_equipamento_modelo, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['TESTE_' . time(), 'Equipamento Teste', $testValue]);
            
            echo "✅ Inserção com valor '$testValue' funcionou!\n";
            
            // Verificar se foi inserido
            $lastId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT tipo_equipamento_modelo FROM motores WHERE id = ?");
            $stmt->execute([$lastId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "✅ Valor recuperado: '" . $result['tipo_equipamento_modelo'] . "'\n";
            
            // Limpar teste
            $pdo->exec("DELETE FROM motores WHERE id = $lastId");
            echo "✅ Registro de teste removido.\n";
            
        } catch (PDOException $e) {
            echo "❌ Erro na inserção: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Campo 'tipo_equipamento_modelo' NÃO existe!\n";
        
        // Verificar se existe como tipo_equipamento
        $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE 'tipo_equipamento'");
        if ($stmt->rowCount() > 0) {
            echo "⚠️  Campo 'tipo_equipamento' existe. Pode ser necessário renomear.\n";
        }
    }
    
    // 5. Verificar logs de erro do Laravel
    echo "\n📋 Verificando logs de erro...\n";
    $logFile = __DIR__ . '/storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $lastLines = file($logFile);
        $recentErrors = array_slice($lastLines, -20); // Últimas 20 linhas
        
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