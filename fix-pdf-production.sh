#!/bin/bash

# Script para corrigir problemas de PDF na produção
# Execute: bash fix-pdf-production.sh

echo "🔧 CORREÇÃO DE PDF - PRODUÇÃO"
echo "============================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Execute este script no diretório raiz do Laravel"
    exit 1
fi

echo "📁 Diretório atual: $(pwd)"
echo "🌍 Ambiente: $(php artisan env)"

# 1. Criar diretórios temporários necessários
echo ""
echo "1. Criando diretórios temporários..."
sudo mkdir -p /tmp/browsershot-temp
sudo chmod 755 /tmp/browsershot-temp
sudo chown www-data:www-data /tmp/browsershot-temp

sudo mkdir -p /tmp/chromium-data
sudo chmod 755 /tmp/chromium-data
sudo chown www-data:www-data /tmp/chromium-data

echo "✅ Diretórios criados"

# 2. Verificar instalação do Chromium
echo ""
echo "2. Verificando Chromium..."
CHROMIUM_PATHS=(
    "/snap/bin/chromium"
    "/usr/bin/chromium-browser"
    "/usr/bin/chromium"
    "/usr/bin/google-chrome-stable"
    "/usr/bin/google-chrome"
)

CHROMIUM_FOUND=""
for path in "${CHROMIUM_PATHS[@]}"; do
    if [ -f "$path" ]; then
        echo "✅ Encontrado: $path"
        CHROMIUM_FOUND="$path"
        # Testar versão
        VERSION=$($path --version 2>/dev/null || echo "Erro ao obter versão")
        echo "   Versão: $VERSION"
        break
    else
        echo "❌ Não encontrado: $path"
    fi
done

if [ -z "$CHROMIUM_FOUND" ]; then
    echo "⚠️  Nenhum Chromium encontrado. Instalando..."
    sudo snap install chromium
    CHROMIUM_FOUND="/snap/bin/chromium"
fi

# 3. Configurar permissões
echo ""
echo "3. Configurando permissões..."
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chmod -R 755 bootstrap/cache/

echo "✅ Permissões configuradas"

# 4. Limpar cache
echo ""
echo "4. Limpando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✅ Cache limpo"

# 5. Testar geração de PDF
echo ""
echo "5. Testando PDF..."
php artisan pdf:diagnosticar

# 6. Verificar logs
echo ""
echo "6. Verificando logs recentes..."
echo "Últimas 10 linhas do log:"
tail -n 10 storage/logs/laravel.log

echo ""
echo "🎉 CORREÇÃO CONCLUÍDA!"
echo "====================="
echo ""
echo "📋 Próximos passos:"
echo "1. Teste a geração de PDF acessando: /relatorios/{ID}/pdf-browsershot"
echo "2. Se ainda houver erro, verifique os logs: tail -f storage/logs/laravel.log"
echo "3. Execute o diagnóstico: php artisan pdf:diagnosticar {ID_RELATORIO}"
echo ""
echo "🔧 Chromium encontrado em: $CHROMIUM_FOUND"
echo "📁 Diretórios temporários criados em /tmp/"
echo "✅ Sistema pronto para geração de PDF!"
