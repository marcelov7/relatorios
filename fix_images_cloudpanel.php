<?php
/**
 * Script de Correção Automática - Sistema de Imagens CloudPanel
 * Execute: php fix_images_cloudpanel.php
 */

echo "🔧 === CORREÇÃO AUTOMÁTICA - SISTEMA DE IMAGENS ===\n\n";

$correcoes = [];
$erros = [];

// 1. Criar diretórios necessários
echo "📁 1. Criando diretórios necessários...\n";

$diretoriosNecessarios = [
    'storage/app/public/relatorios',
    'storage/app/public/relatorios/thumbs',
    'storage/app/public/atualizacoes',
    'public/storage/relatorios',
    'public/storage/relatorios/thumbs',
    'public/storage/atualizacoes'
];

foreach ($diretoriosNecessarios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0775, true)) {
            echo "   ✅ Criado: {$dir}\n";
            $correcoes[] = "Diretório criado: {$dir}";
        } else {
            echo "   ❌ Erro ao criar: {$dir}\n";
            $erros[] = "Não foi possível criar: {$dir}";
        }
    } else {
        echo "   ℹ️  Já existe: {$dir}\n";
    }
}

// 2. Ajustar permissões
echo "\n🔐 2. Ajustando permissões...\n";

$diretoriosPermissoes = [
    'storage/app/public',
    'storage/app/public/relatorios',
    'storage/app/public/relatorios/thumbs',
    'storage/app/public/atualizacoes'
];

foreach ($diretoriosPermissoes as $dir) {
    if (is_dir($dir)) {
        if (chmod($dir, 0775)) {
            echo "   ✅ Permissões ajustadas: {$dir}\n";
            $correcoes[] = "Permissões ajustadas: {$dir}";
        } else {
            echo "   ⚠️  Erro ao ajustar permissões: {$dir}\n";
            $erros[] = "Erro ao ajustar permissões: {$dir}";
        }
    }
}

// 3. Verificar/criar link simbólico
echo "\n🔗 3. Verificando link simbólico...\n";

if (!is_link('public/storage')) {
    // Remover se for diretório comum
    if (is_dir('public/storage') && !is_link('public/storage')) {
        echo "   🗑️  Removendo diretório conflitante...\n";
        rmdir('public/storage');
    }
    
    $target = realpath('storage/app/public');
    if (symlink($target, 'public/storage')) {
        echo "   ✅ Link simbólico criado: public/storage -> {$target}\n";
        $correcoes[] = "Link simbólico criado";
    } else {
        echo "   ❌ Erro ao criar link simbólico\n";
        $erros[] = "Erro ao criar link simbólico";
    }
} else {
    echo "   ℹ️  Link simbólico já existe\n";
}

// 4. Sincronizar imagens existentes
echo "\n🔄 4. Sincronizando imagens existentes...\n";

$origemDir = 'storage/app/public/relatorios';
$destinoDir = 'public/storage/relatorios';

if (is_dir($origemDir) && is_dir($destinoDir)) {
    $imagens = glob($origemDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $imagensSincronizadas = 0;
    
    foreach ($imagens as $imagemOrigem) {
        $nomeArquivo = basename($imagemOrigem);
        $imagemDestino = $destinoDir . '/' . $nomeArquivo;
        
        if (!file_exists($imagemDestino)) {
            if (copy($imagemOrigem, $imagemDestino)) {
                $imagensSincronizadas++;
            }
        }
    }
    
    // Sincronizar thumbnails
    $thumbsOrigem = $origemDir . '/thumbs';
    $thumbsDestino = $destinoDir . '/thumbs';
    
    if (is_dir($thumbsOrigem) && is_dir($thumbsDestino)) {
        $thumbs = glob($thumbsOrigem . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        foreach ($thumbs as $thumbOrigem) {
            $nomeArquivo = basename($thumbOrigem);
            $thumbDestino = $thumbsDestino . '/' . $nomeArquivo;
            
            if (!file_exists($thumbDestino)) {
                if (copy($thumbOrigem, $thumbDestino)) {
                    $imagensSincronizadas++;
                }
            }
        }
    }
    
    if ($imagensSincronizadas > 0) {
        echo "   ✅ {$imagensSincronizadas} imagens sincronizadas\n";
        $correcoes[] = "{$imagensSincronizadas} imagens sincronizadas";
    } else {
        echo "   ℹ️  Todas as imagens já estão sincronizadas\n";
    }
} else {
    echo "   ⚠️  Diretórios de origem ou destino não encontrados\n";
}

// 5. Criar arquivo .htaccess para uploads (se não existir)
echo "\n📄 5. Verificando arquivo .htaccess...\n";

$htaccessPublic = 'public/.htaccess';
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Configurações para uploads
<IfModule mod_php.c>
    php_value upload_max_filesize 20M
    php_value post_max_size 20M
    php_value max_execution_time 300
    php_value memory_limit 256M
</IfModule>
EOT;

if (!file_exists($htaccessPublic)) {
    if (file_put_contents($htaccessPublic, $htaccessContent)) {
        echo "   ✅ Arquivo .htaccess criado\n";
        $correcoes[] = "Arquivo .htaccess criado";
    } else {
        echo "   ❌ Erro ao criar .htaccess\n";
        $erros[] = "Erro ao criar .htaccess";
    }
} else {
    echo "   ℹ️  Arquivo .htaccess já existe\n";
}

// 6. Testar criação de arquivo
echo "\n🧪 6. Testando sistema de arquivos...\n";

$testFile = 'storage/app/public/test_system.txt';
$testContent = 'Teste do sistema - ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "   ✅ Sistema de arquivos funcionando\n";
    
    // Testar sincronização
    $testPublic = 'public/storage/test_system.txt';
    if (copy($testFile, $testPublic)) {
        echo "   ✅ Sincronização funcionando\n";
        unlink($testPublic);
    } else {
        echo "   ⚠️  Erro na sincronização\n";
        $erros[] = "Erro na sincronização de arquivos";
    }
    
    unlink($testFile);
} else {
    echo "   ❌ Erro no sistema de arquivos\n";
    $erros[] = "Sistema de arquivos com problemas";
}

// 7. Verificar configurações críticas
echo "\n⚙️ 7. Verificando configurações críticas...\n";

// Verificar extensão GD
if (extension_loaded('gd')) {
    echo "   ✅ Extensão GD disponível\n";
} else {
    echo "   ❌ Extensão GD não encontrada\n";
    $erros[] = "Extensão GD não instalada";
}

// Verificar fileinfo
if (extension_loaded('fileinfo')) {
    echo "   ✅ Extensão fileinfo disponível\n";
} else {
    echo "   ❌ Extensão fileinfo não encontrada\n";
    $erros[] = "Extensão fileinfo não instalada";
}

// Resumo final
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 RESUMO DA CORREÇÃO\n";
echo str_repeat("=", 60) . "\n";

if (!empty($correcoes)) {
    echo "✅ CORREÇÕES APLICADAS:\n\n";
    foreach ($correcoes as $i => $correcao) {
        echo "   " . ($i + 1) . ". {$correcao}\n";
    }
}

if (!empty($erros)) {
    echo "\n❌ PROBLEMAS QUE PRECISAM DE ATENÇÃO MANUAL:\n\n";
    foreach ($erros as $i => $erro) {
        echo "   " . ($i + 1) . ". {$erro}\n";
    }
    
    echo "\n🔧 COMANDOS ADICIONAIS NECESSÁRIOS:\n\n";
    
    if (in_array('Extensão GD não instalada', $erros)) {
        echo "   # Instalar extensão GD (Ubuntu/Debian)\n";
        echo "   sudo apt-get install php-gd\n";
        echo "   sudo systemctl restart apache2  # ou nginx\n\n";
    }
    
    if (strpos(implode(' ', $erros), 'permissões') !== false) {
        echo "   # Ajustar propriedade dos arquivos (como root)\n";
        echo "   sudo chown -R www-data:www-data storage/\n";
        echo "   sudo chown -R www-data:www-data public/storage/\n\n";
    }
}

echo "\n🚀 PRÓXIMOS PASSOS:\n";
echo "   1. Execute: php artisan storage:link\n";
echo "   2. Teste upload via interface web\n";
echo "   3. Verifique logs: tail -f storage/logs/laravel.log\n";
echo "   4. Execute verificação: php check_images_cloudpanel.php\n\n";

if (empty($erros)) {
    echo "🎉 SISTEMA CORRIGIDO! Pronto para uploads no CloudPanel.\n";
} else {
    echo "⚠️  CORREÇÕES PARCIAIS. Verifique os problemas manuais acima.\n";
}

echo "\n✅ Correção concluída!\n";
