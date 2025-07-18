# Script de Deploy para CloudPanel - Sistema de Motores
# PowerShell Version - Deploy Simples
# Autor: Sistema de Relatórios

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "🚀 DEPLOY CLOUDPANEL - Sistema de Motores" -ForegroundColor Green
Write-Host "🌐 Terminal - Deploy Automatizado" -ForegroundColor Yellow
Write-Host "===========================================" -ForegroundColor Cyan

# Solicitar informações do CloudPanel
Write-Host "Configurações do CloudPanel:" -ForegroundColor Cyan
$cloudPanelUser = Read-Host "Usuário SSH do CloudPanel"
$cloudPanelHost = Read-Host "Host/IP do servidor"
$cloudPanelPath = Read-Host "Caminho do projeto no servidor (ex: /home/cloudpanel/htdocs/seu-dominio.com)"
$cloudPanelDomain = Read-Host "Domínio do site (ex: seu-dominio.com)"

Write-Host ""
Write-Host "Configurações:" -ForegroundColor Green
Write-Host "   👤 Usuário: $cloudPanelUser" -ForegroundColor White
Write-Host "   🌐 Host: $cloudPanelHost" -ForegroundColor White
Write-Host "   📁 Caminho: $cloudPanelPath" -ForegroundColor White
Write-Host "   🔗 Domínio: $cloudPanelDomain" -ForegroundColor White

$confirm = Read-Host "Confirmar e continuar? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Host "Deploy cancelado." -ForegroundColor Red
    exit 0
}

Write-Host ""
Write-Host "Iniciando deploy..." -ForegroundColor Green

# 1. Testar conexão SSH
Write-Host "1. Testando conexão SSH..." -ForegroundColor Green
try {
    $sshTest = ssh -o ConnectTimeout=10 "$cloudPanelUser@$cloudPanelHost" "echo 'Conexão SSH OK'"
    if ($sshTest -eq "Conexão SSH OK") {
        Write-Host "   ✅ Conexão SSH estabelecida" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Falha na conexão SSH" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "   ❌ Erro na conexão SSH" -ForegroundColor Red
    Write-Host "   Verifique as credenciais e acesso SSH" -ForegroundColor Yellow
    exit 1
}

# 2. Backup do banco
Write-Host "2. Backup do banco de dados..." -ForegroundColor Green
$backupDbResponse = Read-Host "Criar backup do banco? (y/n)"
if ($backupDbResponse -eq "y" -or $backupDbResponse -eq "Y") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupFile = "backup_motores_" + $timestamp + ".sql"
    $backupCommand = "cd $cloudPanelPath && mysqldump -u appuser -p'M@rcelo1809@3033' relatodb > $backupFile"
    
    Write-Host "   - Executando backup..." -ForegroundColor Green
    ssh "$cloudPanelUser@$cloudPanelHost" $backupCommand
    Write-Host "   - Backup criado: $backupFile" -ForegroundColor Green
}

# 3. Deploy via Git
Write-Host "3. Deploy via Git..." -ForegroundColor Green
Write-Host "   - Atualizando código..." -ForegroundColor Green
$gitCommands = @(
    "cd $cloudPanelPath",
    "git fetch origin",
    "git reset --hard origin/master",
    "git clean -fd"
)
$gitCommand = $gitCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $gitCommand
Write-Host "   - Código atualizado" -ForegroundColor Green

# 4. Configuração do ambiente
Write-Host "4. Configurando ambiente..." -ForegroundColor Green
$envCommands = @(
    "cd $cloudPanelPath",
    "cp env-production.txt .env",
    "sed -i 's|https://seu-dominio.com|https://$cloudPanelDomain|g' .env",
    "php artisan key:generate --force"
)
$envCommand = $envCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $envCommand
Write-Host "   - Ambiente configurado" -ForegroundColor Green

# 5. Dependências
Write-Host "5. Instalando dependências..." -ForegroundColor Green
$depCommands = @(
    "cd $cloudPanelPath",
    "composer install --optimize-autoloader --no-dev",
    "npm ci",
    "npm run build"
)
$depCommand = $depCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $depCommand
Write-Host "   - Dependências instaladas" -ForegroundColor Green

# 6. Migrações
Write-Host "6. Executando migrações..." -ForegroundColor Green
$migrateCommands = @(
    "cd $cloudPanelPath",
    "php artisan migrate --force"
)
$migrateCommand = $migrateCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $migrateCommand
Write-Host "   - Migrações executadas" -ForegroundColor Green

# 7. Otimizações
Write-Host "7. Aplicando otimizações..." -ForegroundColor Green
$optimizeCommands = @(
    "cd $cloudPanelPath",
    "php artisan storage:link",
    "php artisan cache:clear",
    "php artisan config:clear",
    "php artisan route:clear",
    "php artisan view:clear",
    "php artisan config:cache",
    "php artisan route:cache",
    "php artisan view:cache"
)
$optimizeCommand = $optimizeCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $optimizeCommand
Write-Host "   - Otimizações aplicadas" -ForegroundColor Green

# 8. Permissões
Write-Host "8. Configurando permissões..." -ForegroundColor Green
$permCommands = @(
    "cd $cloudPanelPath",
    "chmod -R 755 storage bootstrap/cache",
    "chmod -R 775 storage/app/public",
    "mkdir -p storage/app/public/motores"
)
$permCommand = $permCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $permCommand
Write-Host "   - Permissões configuradas" -ForegroundColor Green

# 9. Verificações
Write-Host "9. Verificações finais..." -ForegroundColor Green
$checkCommands = @(
    "cd $cloudPanelPath",
    "php artisan migrate:status",
    "ls -la app/Models/Motor.php",
    "ls -la app/Http/Controllers/MotorController.php"
)
$checkCommand = $checkCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $checkCommand
Write-Host "   - Verificações concluídas" -ForegroundColor Green

# 10. Informações finais
Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "✅ DEPLOY NO CLOUDPANEL CONCLUÍDO!" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "🌐 Informações do deploy:" -ForegroundColor Cyan
Write-Host "   🔗 URL: https://$cloudPanelDomain" -ForegroundColor White
Write-Host "   🚗 Motores: https://$cloudPanelDomain/motores" -ForegroundColor White
Write-Host "   📁 Caminho: $cloudPanelPath" -ForegroundColor White
Write-Host "   👤 Usuário: $cloudPanelUser" -ForegroundColor White

Write-Host ""
Write-Host "🧪 Testes recomendados:" -ForegroundColor Cyan
Write-Host "   1. Acessar: https://$cloudPanelDomain" -ForegroundColor White
Write-Host "   2. Login: admin@sistema.com / admin123" -ForegroundColor White
Write-Host "   3. Testar: https://$cloudPanelDomain/motores" -ForegroundColor White
Write-Host "   4. Criar um motor de teste" -ForegroundColor White
Write-Host "   5. Testar filtros e upload" -ForegroundColor White

Write-Host ""
Write-Host "🔧 Comandos úteis:" -ForegroundColor Cyan
Write-Host "   - SSH: ssh $cloudPanelUser@$cloudPanelHost" -ForegroundColor White
Write-Host "   - Logs: tail -f $cloudPanelPath/storage/logs/laravel.log" -ForegroundColor White
Write-Host "   - Cache: cd $cloudPanelPath && php artisan cache:clear" -ForegroundColor White

Write-Host ""
Write-Host "Deploy finalizado! Sistema de motores ativo no CloudPanel. 🚀" -ForegroundColor Green 