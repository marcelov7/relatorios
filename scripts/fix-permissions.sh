#!/bin/bash

# Script para corrigir permissões do Laravel (CloudPanel/Ubuntu)
# Uso: sudo bash scripts/fix-permissions.sh

set -e

LARAVEL_ROOT=$(dirname "$0")/..
cd "$LARAVEL_ROOT"

# Ajustar dono para www-data
chown -R www-data:www-data storage bootstrap/cache public/storage

# Permissões de diretórios: leitura, escrita e execução para dono e grupo
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
find public/storage -type d -exec chmod 775 {} \;

# Permissões de arquivos: leitura e escrita para dono e grupo
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;
find public/storage -type f -exec chmod 664 {} \;

# Permissão de execução para binários do node_modules (build frontend)
if [ -d node_modules ]; then
  find node_modules/.bin -type f -exec chmod +x {} \; 2>/dev/null || true
fi

# Mensagem final
 echo "Permissões corrigidas para storage, bootstrap/cache e public/storage (dono: www-data)." 