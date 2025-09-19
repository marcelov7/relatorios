#!/bin/bash

# Script para corrigir permissões sem privilégios root
# Execute como usuário do domínio

echo "=== Corrigindo permissões do Laravel (sem root) ==="

# Definir variáveis
APP_PATH="/home/devaxis-app/htdocs/app.devaxis.com.br"

echo "Caminho da aplicação: $APP_PATH"

# Verificar se o diretório existe
if [ ! -d "$APP_PATH" ]; then
    echo "ERRO: Diretório da aplicação não encontrado: $APP_PATH"
    exit 1
fi

# 1. Criar diretórios necessários
echo "1. Criando diretórios necessários..."
mkdir -p "$APP_PATH/storage/logs"
mkdir -p "$APP_PATH/storage/app/public"
mkdir -p "$APP_PATH/storage/framework/cache"
mkdir -p "$APP_PATH/storage/framework/sessions"
mkdir -p "$APP_PATH/storage/framework/views"
mkdir -p "$APP_PATH/bootstrap/cache"

# 2. Definir permissões para diretórios (máximo que o usuário pode fazer)
echo "2. Definindo permissões para diretórios..."
chmod 755 "$APP_PATH/storage"
chmod 755 "$APP_PATH/storage/logs"
chmod 755 "$APP_PATH/storage/app"
chmod 755 "$APP_PATH/storage/app/public"
chmod 755 "$APP_PATH/storage/framework"
chmod 755 "$APP_PATH/storage/framework/cache"
chmod 755 "$APP_PATH/storage/framework/sessions"
chmod 755 "$APP_PATH/storage/framework/views"
chmod 755 "$APP_PATH/bootstrap/cache"

# 3. Criar arquivo de log se não existir
echo "3. Criando arquivo de log..."
touch "$APP_PATH/storage/logs/laravel.log"
chmod 664 "$APP_PATH/storage/logs/laravel.log"

# 4. Verificar se o link simbólico do storage existe
echo "4. Verificando link simbólico do storage..."
if [ ! -L "$APP_PATH/public/storage" ]; then
    echo "Criando link simbólico do storage..."
    cd "$APP_PATH"
    php artisan storage:link
fi

# 5. Limpar cache
echo "5. Limpando cache..."
cd "$APP_PATH"
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "=== Operações concluídas! ==="
echo ""
echo "IMPORTANTE: Se ainda houver problemas de permissão, execute como root:"
echo "sudo chown -R devaxis-app:devaxis-app $APP_PATH"
echo "sudo chmod -R 775 $APP_PATH/storage"
echo "sudo chmod -R 775 $APP_PATH/bootstrap/cache" 