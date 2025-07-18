#!/bin/bash

# Script de Deploy para CloudPanel - Sistema de Motores
# Bash Version - Deploy via Terminal
# Autor: Sistema de Relatórios

echo "==========================================="
echo "🚀 DEPLOY CLOUDPANEL - Sistema de Motores"
echo "🌐 Terminal - Deploy Automatizado"
echo "==========================================="

# Solicitar informações do CloudPanel
echo "Configurações do CloudPanel:"
read -p "Usuário SSH do CloudPanel: " cloudPanelUser
read -p "Host/IP do servidor: " cloudPanelHost
read -p "Caminho do projeto no servidor (ex: /home/cloudpanel/htdocs/seu-dominio.com): " cloudPanelPath
read -p "Domínio do site (ex: seu-dominio.com): " cloudPanelDomain

echo ""
echo "Configurações:"
echo "   👤 Usuário: $cloudPanelUser"
echo "   🌐 Host: $cloudPanelHost"
echo "   📁 Caminho: $cloudPanelPath"
echo "   🔗 Domínio: $cloudPanelDomain"

read -p "Confirmar e continuar? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Deploy cancelado."
    exit 0
fi

echo ""
echo "Iniciando deploy..."

# 1. Testar conexão SSH
echo "1. Testando conexão SSH..."
if ssh -o ConnectTimeout=10 "$cloudPanelUser@$cloudPanelHost" "echo 'Conexão SSH OK'"; then
    echo "   ✅ Conexão SSH estabelecida"
else
    echo "   ❌ Falha na conexão SSH"
    echo "   Verifique as credenciais e acesso SSH"
    exit 1
fi

# 2. Backup do banco
echo "2. Backup do banco de dados..."
read -p "Criar backup do banco? (y/n): " backupDbResponse
if [[ $backupDbResponse == "y" || $backupDbResponse == "Y" ]]; then
    timestamp=$(date +%Y%m%d_%H%M%S)
    backupFile="backup_motores_$timestamp.sql"
    
    echo "   - Executando backup..."
    ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && mysqldump -u appuser -p'M@rcelo1809@3033' relatodb > $backupFile"
    echo "   - Backup criado: $backupFile"
fi

# 3. Deploy via Git
echo "3. Deploy via Git..."
echo "   - Atualizando código..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && git fetch origin && git reset --hard origin/master && git clean -fd"
echo "   - Código atualizado"

# 4. Configuração do ambiente
echo "4. Configurando ambiente..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && cp env-production.txt .env && sed -i 's|https://seu-dominio.com|https://$cloudPanelDomain|g' .env && php artisan key:generate --force"
echo "   - Ambiente configurado"

# 5. Dependências
echo "5. Instalando dependências..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && composer install --optimize-autoloader --no-dev && npm ci && npm run build"
echo "   - Dependências instaladas"

# 6. Migrações
echo "6. Executando migrações..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && php artisan migrate --force"
echo "   - Migrações executadas"

# 7. Otimizações
echo "7. Aplicando otimizações..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && php artisan storage:link && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"
echo "   - Otimizações aplicadas"

# 8. Permissões
echo "8. Configurando permissões..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && chmod -R 755 storage bootstrap/cache && chmod -R 775 storage/app/public && mkdir -p storage/app/public/motores"
echo "   - Permissões configuradas"

# 9. Verificações
echo "9. Verificações finais..."
ssh "$cloudPanelUser@$cloudPanelHost" "cd $cloudPanelPath && php artisan migrate:status && ls -la app/Models/Motor.php && ls -la app/Http/Controllers/MotorController.php"
echo "   - Verificações concluídas"

# 10. Informações finais
echo ""
echo "==========================================="
echo "✅ DEPLOY NO CLOUDPANEL CONCLUÍDO!"
echo "==========================================="
echo ""

echo "🌐 Informações do deploy:"
echo "   🔗 URL: https://$cloudPanelDomain"
echo "   🚗 Motores: https://$cloudPanelDomain/motores"
echo "   📁 Caminho: $cloudPanelPath"
echo "   👤 Usuário: $cloudPanelUser"

echo ""
echo "🧪 Testes recomendados:"
echo "   1. Acessar: https://$cloudPanelDomain"
echo "   2. Login: admin@sistema.com / admin123"
echo "   3. Testar: https://$cloudPanelDomain/motores"
echo "   4. Criar um motor de teste"
echo "   5. Testar filtros e upload"

echo ""
echo "🔧 Comandos úteis:"
echo "   - SSH: ssh $cloudPanelUser@$cloudPanelHost"
echo "   - Logs: tail -f $cloudPanelPath/storage/logs/laravel.log"
echo "   - Cache: cd $cloudPanelPath && php artisan cache:clear"

echo ""
echo "Deploy finalizado! Sistema de motores ativo no CloudPanel. 🚀" 