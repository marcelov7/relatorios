<?php
/**
 * Script de Verificação - Sistema de Imagens CloudPanel
 * Execute: php check_images_cloudpanel.php
 */

echo "🖼️ === VERIFICAÇÃO DO SISTEMA DE IMAGENS - CLOUDPANEL ===\n\n";

// 1. Verificar estrutura de diretórios
echo "📁 1. Verificando estrutura de diretórios...\n";

$diretoriosNecessarios = [
    'storage/app/public',
    'storage/app/public/relatorios',
    'storage/app/public/relatorios/thumbs',
    'storage/app/public/atualizacoes',
    'public/storage',
    'public/storage/relatorios',
    'public/storage/relatorios/thumbs',
    'public/storage/atualizacoes'
];

$problemas = [];

foreach ($diretoriosNecessarios as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ {$dir}\n";
    } else {
        echo "   ❌ {$dir} - NÃO EXISTE\n";
        $problemas[] = "Diretório ausente: {$dir}";
    }
}

// 2. Verificar permissões
echo "\n🔐 2. Verificando permissões...\n";

$diretoriosPermissoes = [
    'storage/app/public',
    'public/storage'
];

foreach ($diretoriosPermissoes as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        if ($perms >= '0755') {
            echo "   ✅ {$dir} - {$perms}\n";
        } else {
            echo "   ⚠️  {$dir} - {$perms} (recomendado: 0775)\n";
            $problemas[] = "Permissões insuficientes: {$dir} ({$perms})";
        }
    }
}

// 3. Verificar link simbólico
echo "\n🔗 3. Verificando link simbólico...\n";

if (is_link('public/storage')) {
    $target = readlink('public/storage');
    echo "   ✅ Link simbólico existe: public/storage -> {$target}\n";
} else {
    echo "   ❌ Link simbólico NÃO existe\n";
    $problemas[] = "Link simbólico ausente";
}

// 4. Verificar configurações PHP
echo "\n⚙️ 4. Verificando configurações PHP...\n";

$phpConfigs = [
    'upload_max_filesize' => '20M',
    'post_max_size' => '20M',
    'max_execution_time' => '300',
    'memory_limit' => '256M'
];

foreach ($phpConfigs as $config => $recomendado) {
    $valor = ini_get($config);
    echo "   📝 {$config}: {$valor} (recomendado: {$recomendado})\n";
}

// 5. Verificar extensões PHP
echo "\n🧩 5. Verificando extensões PHP...\n";

$extensoesNecessarias = ['gd', 'fileinfo', 'mbstring'];

foreach ($extensoesNecessarias as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ {$ext}\n";
    } else {
        echo "   ❌ {$ext} - NÃO INSTALADA\n";
        $problemas[] = "Extensão PHP ausente: {$ext}";
    }
}

// 6. Verificar espaço em disco
echo "\n💾 6. Verificando espaço em disco...\n";

$espacoLivre = disk_free_space('.');
$espacoTotal = disk_total_space('.');
$percentualLivre = ($espacoLivre / $espacoTotal) * 100;

echo "   📊 Espaço livre: " . formatBytes($espacoLivre) . " (" . round($percentualLivre, 1) . "%)\n";

if ($percentualLivre < 10) {
    echo "   ⚠️  Pouco espaço disponível!\n";
    $problemas[] = "Espaço em disco baixo (" . round($percentualLivre, 1) . "%)";
}

// 7. Testar criação de arquivo (se possível)
echo "\n🧪 7. Testando criação de arquivo...\n";

$testFile = 'storage/app/public/test_upload.txt';
$testContent = 'Teste de upload - ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "   ✅ Criação de arquivo funcionando\n";
    unlink($testFile); // Remover arquivo de teste
} else {
    echo "   ❌ Erro ao criar arquivo de teste\n";
    $problemas[] = "Não é possível criar arquivos em storage/app/public";
}

// 8. Verificar imagens existentes
echo "\n🖼️ 8. Verificando imagens existentes...\n";

$imagensDir = 'storage/app/public/relatorios';
if (is_dir($imagensDir)) {
    $imagens = glob($imagensDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $thumbs = glob($imagensDir . '/thumbs/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    
    echo "   📷 Imagens encontradas: " . count($imagens) . "\n";
    echo "   🔍 Thumbnails encontradas: " . count($thumbs) . "\n";
} else {
    echo "   ⚠️  Diretório de imagens não existe\n";
}

// 9. Verificar configurações do Laravel
echo "\n⚙️ 9. Verificando configurações do Laravel...\n";

if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    $configs = [
        'APP_URL' => 'URL da aplicação',
        'FILESYSTEM_DISK' => 'Disco padrão do filesystem'
    ];
    
    foreach ($configs as $config => $descricao) {
        if (preg_match("/^{$config}=(.+)$/m", $envContent, $matches)) {
            echo "   ✅ {$config}={$matches[1]}\n";
        } else {
            echo "   ⚠️  {$config} não configurado\n";
        }
    }
} else {
    echo "   ❌ Arquivo .env não encontrado\n";
    $problemas[] = "Arquivo .env não encontrado";
}

// Resumo final
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 RESUMO DA VERIFICAÇÃO\n";
echo str_repeat("=", 60) . "\n";

if (empty($problemas)) {
    echo "🎉 TUDO OK! Sistema de imagens está pronto para o CloudPanel.\n";
} else {
    echo "⚠️  PROBLEMAS ENCONTRADOS:\n\n";
    foreach ($problemas as $i => $problema) {
        echo "   " . ($i + 1) . ". {$problema}\n";
    }
    
    echo "\n🔧 COMANDOS PARA CORRIGIR:\n\n";
    
    // Sugestões de correção
    if (in_array('Link simbólico ausente', $problemas)) {
        echo "   # Criar link simbólico\n";
        echo "   php artisan storage:link\n\n";
    }
    
    if (strpos(implode(' ', $problemas), 'Diretório ausente') !== false) {
        echo "   # Criar diretórios\n";
        echo "   mkdir -p storage/app/public/relatorios/thumbs\n";
        echo "   mkdir -p storage/app/public/atualizacoes\n";
        echo "   mkdir -p public/storage/relatorios/thumbs\n";
        echo "   mkdir -p public/storage/atualizacoes\n\n";
    }
    
    if (strpos(implode(' ', $problemas), 'Permissões') !== false) {
        echo "   # Corrigir permissões\n";
        echo "   chmod -R 775 storage/app/public\n";
        echo "   chmod -R 775 public/storage\n";
        echo "   chown -R www-data:www-data storage/\n";
        echo "   chown -R www-data:www-data public/storage/\n\n";
    }
}

echo "\n🔗 PRÓXIMOS PASSOS:\n";
echo "   1. Corrigir problemas encontrados (se houver)\n";
echo "   2. Executar: php artisan storage:link\n";
echo "   3. Testar upload de imagem via interface\n";
echo "   4. Verificar se thumbnails são gerados\n";
echo "   5. Monitorar logs: tail -f storage/logs/laravel.log\n\n";

/**
 * Formatar bytes em formato legível
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

echo "✅ Verificação concluída!\n";
