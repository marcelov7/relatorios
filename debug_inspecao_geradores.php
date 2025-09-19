<?php

// Script para debug da página de inspeção de geradores
require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Configurar o ambiente
$app->bind('path.config', function() {
    return __DIR__ . '/config';
});

$app->bind('path.storage', function() {
    return __DIR__ . '/storage';
});

// Inicializar o Eloquent
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection([
    'driver' => env('DB_CONNECTION', 'sqlite'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== DEBUG INSPEÇÃO DE GERADORES ===\n\n";

try {
    // 1. Verificar se a tabela existe
    echo "1. Verificando se a tabela existe...\n";
    $tableExists = Illuminate\Support\Facades\Schema::hasTable('inspecao_geradores');
    echo "Tabela 'inspecao_geradores' existe: " . ($tableExists ? "SIM" : "NÃO") . "\n\n";
    
    if (!$tableExists) {
        echo "❌ ERRO: A tabela não existe. Execute as migrations primeiro.\n";
        echo "Comando: php artisan migrate\n";
        exit(1);
    }
    
    // 2. Verificar quantos registros existem
    echo "2. Verificando quantidade de registros...\n";
    $count = \App\Models\InspecaoGerador::count();
    echo "Total de registros: $count\n\n";
    
    if ($count === 0) {
        echo "⚠️ PROBLEMA IDENTIFICADO: Não há registros na tabela!\n";
        echo "Isso explica por que a página não carrega dados.\n";
        echo "Solução: Criar pelo menos uma inspeção através da interface.\n\n";
    }
    
    // 3. Se existem registros, mostrar alguns
    if ($count > 0) {
        echo "3. Mostrando os últimos 3 registros...\n";
        $inspecoes = \App\Models\InspecaoGerador::with(['user', 'colaborador', 'setor'])
            ->latest('data_inspecao')
            ->take(3)
            ->get();
            
        foreach ($inspecoes as $inspecao) {
            echo "--- Inspeção #{$inspecao->id} ---\n";
            echo "Data: {$inspecao->data_inspecao}\n";
            echo "Colaborador: " . ($inspecao->colaborador ? $inspecao->colaborador->name : 'NULL') . "\n";
            echo "Setor: " . ($inspecao->setor ? $inspecao->setor->nome : ($inspecao->setor_text ?: 'NULL')) . "\n";
            echo "Tensão A: {$inspecao->tensao_a}\n";
            echo "Frequência: {$inspecao->frequencia}\n";
            echo "Pressão óleo: {$inspecao->pressao_oleo}\n";
            echo "Temperatura água: {$inspecao->temperatura_agua}\n";
            echo "Nível combustível: {$inspecao->nivel_combustivel}\n";
            echo "Situação: {$inspecao->situacao}\n";
            echo "\n";
        }
    }
    
    // 4. Verificar estrutura da tabela
    echo "4. Verificando estrutura da tabela...\n";
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('inspecao_geradores');
    echo "Colunas existentes: " . implode(', ', $columns) . "\n\n";
    
    // 5. Verificar tabelas relacionadas
    echo "5. Verificando tabelas relacionadas...\n";
    $usersCount = \App\Models\User::count();
    $setoresCount = \App\Models\Setor::count();
    echo "Usuários: $usersCount\n";
    echo "Setores: $setoresCount\n\n";
    
    // 6. Testar o controller
    echo "6. Testando o método index do controller...\n";
    try {
        $controller = new \App\Http\Controllers\InspecaoGeradorController();
        
        // Simular request
        $request = new \Illuminate\Http\Request();
        
        // Capturar a resposta do index
        ob_start();
        $response = $controller->index($request);
        $output = ob_get_clean();
        
        echo "Controller executado com sucesso!\n";
        echo "Tipo de resposta: " . get_class($response) . "\n\n";
        
    } catch (Exception $e) {
        echo "❌ ERRO no controller: " . $e->getMessage() . "\n";
        echo "Arquivo: " . $e->getFile() . "\n";
        echo "Linha: " . $e->getLine() . "\n\n";
    }
    
    echo "=== FIM DO DEBUG ===\n";
    
    // Resumo dos problemas encontrados
    echo "\n=== RESUMO ===\n";
    if ($count === 0) {
        echo "🔍 PROBLEMA PRINCIPAL: Tabela vazia - não há dados para exibir\n";
        echo "💡 SOLUÇÃO: Acesse /inspecao-geradores/create e crie uma nova inspeção\n";
    } else {
        echo "✅ Dados existem na tabela\n";
        echo "🔍 Problema pode estar no frontend (Vue.js) ou nas rotas\n";
        echo "💡 Verifique o console do navegador para erros JavaScript\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
