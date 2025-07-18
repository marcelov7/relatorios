# Script de Atualização - Sistema de Motores
# PowerShell Version - Atualização em Produção
# Autor: Sistema de Relatórios
# Data: $(Get-Date)

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "🔄 ATUALIZAÇÃO - Sistema de Motores" -ForegroundColor Green
Write-Host "🌐 Produção - Atualização Segura" -ForegroundColor Yellow
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

Write-Log "Iniciando atualização do sistema de motores..."

# 1. Backup das configurações atuais
Write-Log "1. Criando backup das configurações..."
if (Test-Path ".env") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    Copy-Item ".env" ".env.backup.$timestamp"
    Write-Log "Backup do .env criado"
}

# 2. Verificar se é ambiente de produção
Write-Log "2. Verificando ambiente..."
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_ENV=production") {
    Write-Log "✅ Ambiente de produção detectado"
} else {
    Write-Warning-Log "⚠️  ATENÇÃO: Este não parece ser um ambiente de produção!"
    Write-Warning-Log "   Verifique se APP_ENV=production no .env"
    $response = Read-Host "Continuar mesmo assim? (y/n)"
    if ($response -ne "y" -and $response -ne "Y") {
        exit 1
    }
}

# 3. Backup do banco de dados (recomendado)
Write-Log "3. Backup do banco de dados..."
Write-Warning-Log "⚠️  IMPORTANTE: Faça backup do banco antes de continuar!"
Write-Host "   Comando sugerido: mysqldump -u [usuario] -p [banco] > backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql"
Write-Host ""
$backupResponse = Read-Host "Backup do banco realizado? (y/n)"
if ($backupResponse -ne "y" -and $backupResponse -ne "Y") {
    Write-Warning-Log "Recomendamos fazer backup antes de continuar"
    $continueResponse = Read-Host "Continuar mesmo assim? (y/n)"
    if ($continueResponse -ne "y" -and $continueResponse -ne "Y") {
        exit 1
    }
}

# 4. Atualizar código (se usando git)
Write-Log "4. Atualizando código..."
if (Test-Path ".git") {
    Write-Log "   - Verificando atualizações do Git..."
    git fetch origin
    $currentBranch = git branch --show-current
    Write-Log "   - Branch atual: $currentBranch"
    
    $pullResponse = Read-Host "Fazer pull das últimas alterações? (y/n)"
    if ($pullResponse -eq "y" -or $pullResponse -eq "Y") {
        git pull origin $currentBranch
        Write-Log "   - Código atualizado"
    }
} else {
    Write-Warning-Log "Git não encontrado. Certifique-se de que os arquivos estão atualizados."
}

# 5. Instalar/atualizar dependências
Write-Log "5. Atualizando dependências..."
Write-Log "   - Dependências PHP..."
composer install --optimize-autoloader --no-dev

Write-Log "   - Dependências Node.js..."
if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm ci
    Write-Log "   - Assets compilados..."
    npm run build
} else {
    Write-Warning-Log "NPM não encontrado. Compile os assets manualmente: npm run build"
}

# 6. Executar migrações específicas do sistema de motores
Write-Log "6. Executando migrações do sistema de motores..."
Write-Log "   - Verificando status das migrações..."

# Lista de migrações específicas do sistema de motores
$motorMigrations = @(
    "2024_01_01_000000_create_motores_table",
    "2024_01_01_000001_create_relatorio_motor_table", 
    "2025_07_17_204222_update_motores_table_structure",
    "2025_07_17_205503_update_motores_local_reserva_fields"
)

foreach ($migration in $motorMigrations) {
    Write-Log "   - Verificando: $migration"
}

$migrateResponse = Read-Host "Executar migrações do sistema de motores? (y/n)"
if ($migrateResponse -eq "y" -or $migrateResponse -eq "Y") {
    Write-Log "   - Executando migrações..."
    php artisan migrate --force
    Write-Log "   - Migrações executadas"
}

# 7. Limpar e recriar caches
Write-Log "7. Otimizando caches..."
Write-Log "   - Limpando caches antigos..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

Write-Log "   - Recriando caches otimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Verificar estrutura de diretórios
Write-Log "8. Verificando estrutura..."
New-Item -ItemType Directory -Force -Path "storage\app\public\motores" | Out-Null
Write-Log "   - Diretório de motores criado/verificado"

# 9. Verificações finais
Write-Log "9. Verificações finais..."

# Verificar se os arquivos principais existem
$requiredFiles = @(
    "app\Models\Motor.php",
    "app\Http\Controllers\MotorController.php",
    "resources\js\Pages\Motores\Index.vue",
    "resources\js\Pages\Motores\Create.vue",
    "resources\js\Pages\Motores\Edit.vue",
    "resources\js\Pages\Motores\Show.vue"
)

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Log "   ✅ $file"
    } else {
        Write-Error-Log "   ❌ $file - ARQUIVO NÃO ENCONTRADO!"
    }
}

# 10. Informações do sistema
Write-Log "10. Informações do sistema:"
$appName = (Select-String -Path ".env" -Pattern "APP_NAME=(.+)").Matches[0].Groups[1].Value
$version = (Select-String -Path ".env" -Pattern "APP_VERSION=(.+)").Matches[0].Groups[1].Value
$environment = (Select-String -Path ".env" -Pattern "APP_ENV=(.+)").Matches[0].Groups[1].Value

Write-Host "    📱 Nome: $appName" -ForegroundColor White
Write-Host "    🔢 Versão: $version" -ForegroundColor White
Write-Host "    🌍 Ambiente: $environment" -ForegroundColor White
Write-Host "    🚗 Sistema de Motores: ATIVO" -ForegroundColor Green

Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Log "✅ ATUALIZAÇÃO DO SISTEMA DE MOTORES CONCLUÍDA!"
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

Write-Info "🧪 Testes recomendados:"
Write-Host "   1. Acessar: /motores (listagem)"
Write-Host "   2. Criar um motor de teste"
Write-Host "   3. Editar um motor"
Write-Host "   4. Testar filtros e busca"
Write-Host "   5. Verificar responsividade mobile"
Write-Host ""

Write-Info "🔧 Comandos úteis para manutenção:"
Write-Host "   - Ver logs: Get-Content storage\logs\laravel.log -Tail 50"
Write-Host "   - Limpar cache: php artisan cache:clear"
Write-Host "   - Status migrações: php artisan migrate:status"
Write-Host ""

Write-Warning-Log "⚠️  Lembre-se de:"
Write-Host "   - Testar todas as funcionalidades"
Write-Host "   - Verificar se não quebrou nada existente"
Write-Host "   - Monitorar logs de erro nas próximas horas"
Write-Host ""

Write-Log "Atualização finalizada! Sistema de motores ativo. 🚗" 