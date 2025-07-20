# Script de Deploy para CloudPanel - Sistema de Motores
# PowerShell Version - Deploy via Terminal
# Autor: Sistema de Relatórios
# Data: $(Get-Date)

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "🚀 DEPLOY CLOUDPANEL - Sistema de Motores" -ForegroundColor Green
Write-Host "🌐 Terminal - Deploy Automatizado" -ForegroundColor Yellow
Write-Host "===========================================" -ForegroundColor Cyan

function Write-Log {
    param($Message)
    Write-Host "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] $Message" -ForegroundColor Green
}

function Write-Error-Log {
    param($Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Write-Warning-Log {
    param($Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Info {
    param($Message)
    Write-Host "[INFO] $Message" -ForegroundColor Cyan
}

# Verificar se estamos no diretório correto
if (-not (Test-Path "artisan")) {
    Write-Error-Log "Arquivo artisan não encontrado. Execute este script no diretório raiz do Laravel."
    exit 1
}

Write-Log "Iniciando deploy no CloudPanel..."

# 1. Configurações do CloudPanel
Write-Log "1. Configurando ambiente CloudPanel..."

# Solicitar informações do CloudPanel
Write-Info "Configurações do CloudPanel:"
$cloudPanelUser = Read-Host "Usuário SSH do CloudPanel"
$cloudPanelHost = Read-Host "Host/IP do servidor"
$cloudPanelPath = Read-Host "Caminho do projeto no servidor (ex: /home/cloudpanel/htdocs/seu-dominio.com)"
$cloudPanelDomain = Read-Host "Domínio do site (ex: seu-dominio.com)"

Write-Log "Configurações salvas:"
Write-Host "   👤 Usuário: $cloudPanelUser" -ForegroundColor White
Write-Host "   🌐 Host: $cloudPanelHost" -ForegroundColor White
Write-Host "   📁 Caminho: $cloudPanelPath" -ForegroundColor White
Write-Host "   🔗 Domínio: $cloudPanelDomain" -ForegroundColor White

$confirm = Read-Host "Confirmar configurações? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Log "Deploy cancelado pelo usuário."
    exit 0
}

# 2. Backup local (opcional)
Write-Log "2. Backup local..."
$backupResponse = Read-Host "Fazer backup local antes do deploy? (y/n)"
if ($backupResponse -eq "y" -or $backupResponse -eq "Y") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupFile = "backup_local_$timestamp.zip"
    
    Write-Log "   - Criando backup local..."
    Compress-Archive -Path "app", "resources", "routes", "database" -DestinationPath $backupFile
    Write-Log "   - Backup criado: $backupFile"
}

# 3. Testar conexão SSH
Write-Log "3. Testando conexão SSH..."
try {
    $sshTest = ssh -o ConnectTimeout=10 -o BatchMode=yes "$cloudPanelUser@$cloudPanelHost" "echo 'Conexão SSH OK'"
    if ($sshTest -eq "Conexão SSH OK") {
        Write-Log "   ✅ Conexão SSH estabelecida"
    } else {
        Write-Error-Log "   ❌ Falha na conexão SSH"
        exit 1
    }
} catch {
    Write-Error-Log "   ❌ Erro na conexão SSH: $($_.Exception.Message)"
    Write-Warning-Log "   Verifique:"
    Write-Host "      - Credenciais SSH corretas" -ForegroundColor Yellow
    Write-Host "      - Chave SSH configurada" -ForegroundColor Yellow
    Write-Host "      - Acesso ao servidor liberado" -ForegroundColor Yellow
    exit 1
}

# 4. Backup remoto do banco
Write-Log "4. Backup remoto do banco de dados..."
Write-Warning-Log "⚠️  IMPORTANTE: Backup do banco será criado no servidor"
$backupDbResponse = Read-Host "Criar backup do banco no servidor? (y/n)"
if ($backupDbResponse -eq "y" -or $backupDbResponse -eq "Y") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupDbCommand = "cd $cloudPanelPath && mysqldump -u appuser -p'M@rcelo1809@3033' relatodb > backup_motores_" + $timestamp + ".sql"
    
    Write-Log "   - Executando backup remoto..."
    ssh "$cloudPanelUser@$cloudPanelHost" $backupDbCommand
    Write-Log "   - Backup do banco criado no servidor"
}

# 5. Deploy via Git
Write-Log "5. Deploy via Git..."
Write-Info "Opções de deploy:"
Write-Host "   1. Pull direto no servidor (recomendado)" -ForegroundColor White
Write-Host "   2. Upload de arquivos via SCP" -ForegroundColor White
Write-Host "   3. Clone/Reset no servidor" -ForegroundColor White

$deployOption = Read-Host "Escolha a opção (1-3)"

switch ($deployOption) {
    "1" {
        Write-Log "   - Deploy via Git Pull..."
        $gitCommands = @(
            "cd $cloudPanelPath",
            "git fetch origin",
            "git reset --hard origin/master",
            "git clean -fd"
        )
        $gitCommand = $gitCommands -join " && "
        ssh "$cloudPanelUser@$cloudPanelHost" $gitCommand
        Write-Log "   - Código atualizado via Git"
    }
    "2" {
        Write-Log "   - Deploy via SCP..."
        Write-Warning-Log "   Esta opção pode demorar dependendo do tamanho dos arquivos"
        
        # Lista de diretórios para upload
        $directories = @("app", "resources", "routes", "database", "public")
        foreach ($dir in $directories) {
            Write-Log "   - Enviando $dir..."
            scp -r "$dir" "$cloudPanelUser@$cloudPanelHost:$cloudPanelPath/"
        }
        Write-Log "   - Arquivos enviados via SCP"
    }
    "3" {
        Write-Log "   - Deploy via Clone/Reset..."
        $cloneCommands = @(
            "cd $cloudPanelPath",
            "git fetch origin",
            "git reset --hard origin/master",
            "git clean -fd"
        )
        $cloneCommand = $cloneCommands -join " && "
        ssh "$cloudPanelUser@$cloudPanelHost" $cloneCommand
        Write-Log "   - Código atualizado via Clone/Reset"
    }
    default {
        Write-Error-Log "   Opção inválida"
        exit 1
    }
}

# 6. Configuração do ambiente
Write-Log "6. Configurando ambiente no servidor..."

$envCommands = @(
    "cd $cloudPanelPath",
    "cp env-production.txt .env",
    "sed -i 's|https://seu-dominio.com|https://$cloudPanelDomain|g' .env",
    "php artisan key:generate --force"
)
$envCommand = $envCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $envCommand
Write-Log "   - Ambiente configurado"

# 7. Dependências e build
Write-Log "7. Instalando dependências..."
$depCommands = @(
    "cd $cloudPanelPath",
    "composer install --optimize-autoloader --no-dev",
    "npm ci",
    "npm run build"
)
$depCommand = $depCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $depCommand
Write-Log "   - Dependências instaladas"

# 8. Migrações
Write-Log "8. Executando migrações..."
$migrateCommands = @(
    "cd $cloudPanelPath",
    "php artisan migrate --force"
)
$migrateCommand = $migrateCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $migrateCommand
Write-Log "   - Migrações executadas"

# 9. Otimizações
Write-Log "9. Aplicando otimizações..."
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
Write-Log "   - Otimizações aplicadas"

# 10. Permissões
Write-Log "10. Configurando permissões..."
$permCommands = @(
    "cd $cloudPanelPath",
    "chmod -R 755 storage bootstrap/cache",
    "chmod -R 775 storage/app/public",
    "mkdir -p storage/app/public/motores"
)
$permCommand = $permCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $permCommand
Write-Log "   - Permissões configuradas"

# 11. Verificações finais
Write-Log "11. Verificações finais..."
$checkCommands = @(
    "cd $cloudPanelPath",
    "php artisan migrate:status",
    "ls -la app/Models/Motor.php",
    "ls -la app/Http/Controllers/MotorController.php",
    "ls -la resources/js/Pages/Motores/"
)
$checkCommand = $checkCommands -join " && "
ssh "$cloudPanelUser@$cloudPanelHost" $checkCommand
Write-Log "   - Verificações concluídas"

# 12. Informações finais
Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Log "✅ DEPLOY NO CLOUDPANEL CONCLUÍDO!"
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

Write-Info "🌐 Informações do deploy:"
Write-Host "   🔗 URL: https://$cloudPanelDomain" -ForegroundColor White
Write-Host "   🚗 Motores: https://$cloudPanelDomain/motores" -ForegroundColor White
Write-Host "   📁 Caminho: $cloudPanelPath" -ForegroundColor White
Write-Host "   👤 Usuário: $cloudPanelUser" -ForegroundColor White

Write-Host ""
Write-Info "🧪 Testes recomendados:"
Write-Host "   1. Acessar: https://$cloudPanelDomain" -ForegroundColor White
Write-Host "   2. Login: admin@sistema.com / admin123" -ForegroundColor White
Write-Host "   3. Testar: https://$cloudPanelDomain/motores" -ForegroundColor White
Write-Host "   4. Criar um motor de teste" -ForegroundColor White
Write-Host "   5. Testar filtros e upload" -ForegroundColor White

Write-Host ""
Write-Info "🔧 Comandos úteis para manutenção:"
Write-Host "   - SSH: ssh $cloudPanelUser@$cloudPanelHost" -ForegroundColor White
Write-Host "   - Logs: tail -f $cloudPanelPath/storage/logs/laravel.log" -ForegroundColor White
Write-Host "   - Cache: cd $cloudPanelPath && php artisan cache:clear" -ForegroundColor White

Write-Host ""
Write-Warning-Log "⚠️  Lembre-se de:"
Write-Host "   - Testar todas as funcionalidades" -ForegroundColor Yellow
Write-Host "   - Verificar SSL/HTTPS" -ForegroundColor Yellow
Write-Host "   - Configurar backup automático" -ForegroundColor Yellow
Write-Host "   - Monitorar logs nas próximas horas" -ForegroundColor Yellow

Write-Host ""
Write-Log "Deploy finalizado! Sistema de motores ativo no CloudPanel. 🚀" 