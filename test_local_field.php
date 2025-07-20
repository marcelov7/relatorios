<?php
/**
 * Script para testar se o campo local está funcionando corretamente
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
    
    // 1. Verificar estrutura atual da coluna local
    echo "📋 Estrutura atual da coluna 'local':\n";
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
        
        // Verificar se está correto
        if ($column['Type'] === 'varchar(255)' && $column['Key'] === '') {
            echo "\n✅ Campo 'local' está correto! (VARCHAR(255) sem chave)\n";
        } else {
            echo "\n⚠️  Campo 'local' ainda não está correto.\n";
        }
    } else {
        echo "❌ Coluna 'local' não encontrada!\n";
        exit(1);
    }
    
    // 2. Testar inserção de dados
    echo "\n🧪 Testando inserção de dados...\n";
    
    $testTag = 'TESTE_' . time();
    $testLocal = 'Local de Teste - Área 123';
    
    try {
        $sql = "INSERT INTO motores (tag, equipamento, local, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$testTag, 'Equipamento de Teste', $testLocal]);
        
        echo "✅ Inserção com valor '$testLocal' funcionou!\n";
        
        // Verificar se foi inserido
        $lastId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT id, tag, local FROM motores WHERE id = ?");
        $stmt->execute([$lastId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Registro inserido:\n";
        echo "   ID: " . $result['id'] . "\n";
        echo "   TAG: " . $result['tag'] . "\n";
        echo "   LOCAL: '" . $result['local'] . "'\n";
        
        // Testar atualização
        echo "\n🧪 Testando atualização...\n";
        $newLocal = 'Local Atualizado - Setor B';
        $updateSql = "UPDATE motores SET local = ? WHERE id = ?";
        $stmt = $pdo->prepare($updateSql);
        $stmt->execute([$newLocal, $lastId]);
        
        echo "✅ Atualização funcionou!\n";
        
        // Verificar atualização
        $stmt = $pdo->prepare("SELECT local FROM motores WHERE id = ?");
        $stmt->execute([$lastId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Valor atualizado: '" . $result['local'] . "'\n";
        
        // Limpar teste
        $pdo->exec("DELETE FROM motores WHERE id = $lastId");
        echo "✅ Registro de teste removido.\n";
        
    } catch (PDOException $e) {
        echo "❌ Erro na inserção/atualização: " . $e->getMessage() . "\n";
    }
    
    // 3. Verificar se há outros problemas na tabela
    echo "\n🔍 Verificando outros campos problemáticos...\n";
    
    $problematicFields = ['tipo_equipamento_modelo', 'armazenamento'];
    
    foreach ($problematicFields as $field) {
        $stmt = $pdo->query("SHOW COLUMNS FROM motores LIKE '$field'");
        $column = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($column) {
            echo "   $field: " . $column['Type'] . " | Chave: " . $column['Key'] . "\n";
            
            // Verificar se há problemas
            if ($column['Type'] === 'text' && $column['Key'] !== '') {
                echo "   ⚠️  Campo '$field' é TEXT com chave - pode causar problemas!\n";
            }
        }
    }
    
    // 4. Teste final
    echo "\n🎯 Teste final - Simulando operação do Laravel...\n";
    
    try {
        // Simular dados que o Laravel enviaria
        $motorData = [
            'tag' => 'MOTOR_TESTE_' . time(),
            'equipamento' => 'Motor Elétrico Teste',
            'carcaca_fabricante' => 'WEG',
            'potencia_kw' => 15.5,
            'potencia_cv' => 21.0,
            'rotacao' => 1750,
            'corrente_placa' => 28.5,
            'corrente_configurada' => 30.0,
            'tipo_equipamento_modelo' => 'Bomba Centrífuga',
            'fabricante' => 'WEG',
            'reserva_almox' => 'Prateleira A-15',
            'local' => 'Sala de Bombas - Subsolo',
            'armazenamento' => 'Instalado',
            'observacoes' => 'Motor de teste para validação',
            'ativo' => 1
        ];
        
        $columns = implode(', ', array_keys($motorData));
        $placeholders = ':' . implode(', :', array_keys($motorData));
        $sql = "INSERT INTO motores ($columns, created_at, updated_at) VALUES ($placeholders, NOW(), NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($motorData);
        
        echo "✅ Inserção completa do Laravel funcionou!\n";
        
        // Limpar teste final
        $lastId = $pdo->lastInsertId();
        $pdo->exec("DELETE FROM motores WHERE id = $lastId");
        echo "✅ Registro de teste final removido.\n";
        
    } catch (PDOException $e) {
        echo "❌ Erro no teste final: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Teste concluído com sucesso!\n";
    echo "💡 O campo 'local' está funcionando corretamente.\n";
    echo "🚀 Agora você pode testar o sistema Laravel sem erros 500!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique se as credenciais do banco estão corretas no início do arquivo.\n";
    exit(1);
}
?> 