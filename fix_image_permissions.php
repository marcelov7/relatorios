<?php

/**
 * Script para corrigir o problema de permissões na substituição de imagens
 * 
 * Problemas identificados:
 * 1. O PHP-FPM está rodando como um usuário que não tem permissão para deletar arquivos de devaxis-app
 * 2. A função unlink() está falhando no ImageUploadService
 * 3. Não consegue criar novas imagens devido a falhas na exclusão das antigas
 */

echo "=== DIAGNÓSTICO DE PERMISSÕES PARA IMAGENS ===\n\n";

// Verificar usuário atual do PHP
echo "1. Usuário PHP atual: " . get_current_user() . "\n";
echo "   Process UID: " . posix_getuid() . "\n";
echo "   Process GID: " . posix_getgid() . "\n\n";

// Verificar diretórios críticos
$directories = [
    'storage/app/public',
    'storage/app/public/relatorios',
    'public/storage',
    'public/storage/relatorios'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = fileperms($dir);
        $owner = posix_getpwuid(fileowner($dir));
        $group = posix_getgrgid(filegroup($dir));
        
        echo "📁 {$dir}:\n";
        echo "   Permissões: " . sprintf('%o', $perms & 0777) . "\n";
        echo "   Proprietário: {$owner['name']} (UID: {$owner['uid']})\n";
        echo "   Grupo: {$group['name']} (GID: {$group['gid']})\n";
        echo "   Escrita permitida: " . (is_writable($dir) ? 'SIM' : 'NÃO') . "\n\n";
    } else {
        echo "❌ {$dir}: DIRETÓRIO NÃO EXISTE\n\n";
    }
}

// Verificar um arquivo específico de teste (relatório 193)
$testFile = 'public/storage/relatorios/193/original/1753488941_68841e2de780d.png';
if (file_exists($testFile)) {
    $perms = fileperms($testFile);
    $owner = posix_getpwuid(fileowner($testFile));
    $group = posix_getgrgid(filegroup($testFile));
    
    echo "🖼️ Arquivo de teste: {$testFile}\n";
    echo "   Permissões: " . sprintf('%o', $perms & 0777) . "\n";
    echo "   Proprietário: {$owner['name']} (UID: {$owner['uid']})\n";
    echo "   Grupo: {$group['name']} (GID: {$group['gid']})\n";
    echo "   Pode deletar: " . (is_writable(dirname($testFile)) ? 'SIM' : 'NÃO') . "\n\n";
} else {
    echo "❌ Arquivo de teste não encontrado: {$testFile}\n\n";
}

echo "=== SUGESTÕES DE CORREÇÃO ===\n\n";

echo "1. EXECUTAR COMO ROOT NO SERVIDOR:\n";
echo "   # Corrigir permissões globais\n";
echo "   chown -R devaxis-app:devaxis-app storage/ public/storage/\n";
echo "   chmod -R 775 storage/app/public public/storage\n\n";

echo "2. MODIFICAR ImageUploadService para usar exec() ou system():\n";
echo "   - Substituir unlink() direto por comando via shell\n";
echo "   - Usar sudo para operações de arquivo críticas\n\n";

echo "3. CRIAR SCRIPT DE LIMPEZA SEPARADO:\n";
echo "   - Script PHP executado como devaxis-app\n";
echo "   - Chamado via cron ou queue job\n\n";

echo "=== TESTE DE CRIAÇÃO DE ARQUIVO ===\n\n";

$testDir = 'storage/app/public/relatorios/test';
$testFilePath = $testDir . '/test_' . time() . '.txt';

try {
    if (!is_dir($testDir)) {
        mkdir($testDir, 0775, true);
        echo "✅ Diretório de teste criado: {$testDir}\n";
    }
    
    file_put_contents($testFilePath, "Teste de escrita: " . date('Y-m-d H:i:s'));
    echo "✅ Arquivo de teste criado: {$testFilePath}\n";
    
    // Tentar deletar
    if (unlink($testFilePath)) {
        echo "✅ Arquivo de teste deletado com sucesso\n";
    } else {
        echo "❌ FALHA ao deletar arquivo de teste\n";
    }
    
    // Limpar diretório
    rmdir($testDir);
    echo "✅ Diretório de teste removido\n";
    
} catch (Exception $e) {
    echo "❌ ERRO no teste: " . $e->getMessage() . "\n";
}

echo "\n=== RELATÓRIO COMPLETO ===\n";
echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "Servidor: " . gethostname() . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Usuário PHP: " . get_current_user() . "\n";
echo "Working Directory: " . getcwd() . "\n";
