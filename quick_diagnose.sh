#!/bin/bash
# Script de Diagnóstico Rápido - Imagens CloudPanel

echo "🖼️ === DIAGNÓSTICO RÁPIDO - SISTEMA DE IMAGENS ==="
echo
echo "📍 Diretório atual: $(pwd)"
echo

# 1. Verificar estrutura de diretórios
echo "📁 1. Estrutura de diretórios:"
echo "   storage/app/public: $([ -d "storage/app/public" ] && echo "✅ OK" || echo "❌ AUSENTE")"
echo "   storage/app/public/relatorios: $([ -d "storage/app/public/relatorios" ] && echo "✅ OK" || echo "❌ AUSENTE")"
echo "   storage/app/public/relatorios/thumbs: $([ -d "storage/app/public/relatorios/thumbs" ] && echo "✅ OK" || echo "❌ AUSENTE")"
echo "   public/storage: $([ -d "public/storage" ] && echo "✅ OK" || echo "❌ AUSENTE")"
echo "   public/storage/relatorios: $([ -d "public/storage/relatorios" ] && echo "✅ OK" || echo "❌ AUSENTE")"

# 2. Verificar link simbólico
echo
echo "🔗 2. Link simbólico:"
if [ -L "public/storage" ]; then
    echo "   ✅ Link existe: public/storage -> $(readlink public/storage)"
else
    echo "   ❌ Link simbólico AUSENTE"
fi

# 3. Verificar permissões
echo
echo "🔐 3. Permissões:"
if [ -d "storage/app/public" ]; then
    echo "   storage/app/public: $(stat -c %a storage/app/public)"
fi
if [ -d "public/storage" ]; then
    echo "   public/storage: $(stat -c %a public/storage)"
fi

# 4. Contar imagens
echo
echo "🖼️ 4. Imagens existentes:"
if [ -d "storage/app/public/relatorios" ]; then
    IMG_COUNT=$(find storage/app/public/relatorios -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" 2>/dev/null | wc -l)
    echo "   📷 Imagens em storage: $IMG_COUNT"
else
    echo "   📷 Diretório de imagens não existe"
fi

if [ -d "public/storage/relatorios" ]; then
    PUB_COUNT=$(find public/storage/relatorios -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" 2>/dev/null | wc -l)
    echo "   🌐 Imagens em public: $PUB_COUNT"
else
    echo "   🌐 Diretório público não existe"
fi

# 5. Testar URL de exemplo
echo
echo "🌐 5. Teste de URL:"
if [ -d "public/storage/relatorios" ] && [ "$(ls -A public/storage/relatorios 2>/dev/null)" ]; then
    SAMPLE_IMG=$(find public/storage/relatorios -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" | head -1)
    if [ -n "$SAMPLE_IMG" ]; then
        REL_PATH=${SAMPLE_IMG#public/}
        echo "   📝 URL de teste: https://app.devaxis.com.br/$REL_PATH"
        echo "   📁 Arquivo físico: $SAMPLE_IMG"
        echo "   📏 Tamanho: $(stat -c %s "$SAMPLE_IMG" 2>/dev/null || echo "erro") bytes"
    fi
fi

echo
echo "🔧 COMANDOS DE CORREÇÃO SUGERIDOS:"
echo

# Verificar se precisa criar link
if [ ! -L "public/storage" ]; then
    echo "   # Criar link simbólico:"
    echo "   php artisan storage:link"
    echo
fi

# Verificar se precisa criar diretórios
if [ ! -d "storage/app/public/relatorios" ]; then
    echo "   # Criar diretórios:"
    echo "   mkdir -p storage/app/public/relatorios/thumbs"
    echo "   mkdir -p storage/app/public/atualizacoes"
    echo
fi

# Permissões
echo "   # Ajustar permissões:"
echo "   chmod -R 775 storage/app/public"
echo "   chmod -R 775 public/storage"
echo "   chown -R www-data:www-data storage/"
echo "   chown -R www-data:www-data public/storage/"
echo

# Sincronizar imagens
echo "   # Sincronizar imagens existentes:"
echo "   cp -r storage/app/public/relatorios/* public/storage/relatorios/ 2>/dev/null || echo 'Sem imagens para copiar'"
echo

echo "✅ Diagnóstico concluído!"
