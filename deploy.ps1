# Script de Deploy para CloudPanel - Sistema de Relatórios V1.00
# PowerShell Version
# Autor: Sistema de Relatórios
# Data: $(Get-Date)

Write-Host "===========================================" -ForegroundColor Cyan
Write-Host "📦 DEPLOY - Sistema de Relatórios V1.00" -ForegroundColor Green
Write-Host "🌐 CloudPanel - Configuração MySQL" -ForegroundColor Yellow
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

Write-Log "Iniciando processo de deploy..."

# 1. Backup das configurações atuais
Write-Log "1. Criando backup das configurações..."
if (Test-Path ".env") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    Copy-Item ".env" ".env.backup.$timestamp"
    Write-Log "Backup do .env criado"
}

# 2. Configurar .env para produção
Write-Log "2. Configurando arquivo .env..."
if (-not (Test-Path ".env")) {
    if (Test-Path "env-production.txt") {
        Copy-Item "env-production.txt" ".env"
        Write-Log "Arquivo .env criado baseado em env-production.txt"
        Write-Warning-Log "⚠️  IMPORTANTE: Ajuste as configurações no .env antes de continuar!"
        Write-Warning-Log "   - APP_URL: https://seu-dominio.com"
        Write-Warning-Log "   - APP_KEY: Execute 'php artisan key:generate'"
        Write-Warning-Log "   - Configurações de email se necessário"
        Write-Host ""
        Read-Host "Pressione ENTER após ajustar o .env"
    } else {
        Write-Error-Log "Arquivo env-production.txt não encontrado!"
        exit 1
    }
}

# 3. Gerar APP_KEY se necessário
Write-Log "3. Verificando APP_KEY..."
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Log "Gerando nova APP_KEY..."
    php artisan key:generate --force
}

# 4. Otimizações para produção
Write-Log "4. Aplicando otimizações para produção..."

# Cache de configuração
Write-Log "   - Cache de configuração..."
php artisan config:cache

# Cache de rotas
Write-Log "   - Cache de rotas..."
php artisan route:cache

# Cache de views
Write-Log "   - Cache de views..."
php artisan view:cache

# Autoload otimizado
Write-Log "   - Otimizando autoload..."
composer install --optimize-autoloader --no-dev

# 5. Configurações de banco de dados
Write-Log "5. Configurando banco de dados..."
Write-Log "   - Testando conexão com o banco..."
php artisan migrate:status

$response = Read-Host "Executar migrações do banco de dados? (y/n)"
if ($response -eq "y" -or $response -eq "Y") {
    Write-Log "   - Executando migrações..."
    php artisan migrate --force
    
    $seedResponse = Read-Host "Executar seeders (dados iniciais)? (y/n)"
    if ($seedResponse -eq "y" -or $seedResponse -eq "Y") {
        Write-Log "   - Executando seeders..."
        php artisan db:seed --force
    }
}

# 6. Configurar storage e permissões
Write-Log "6. Configurando storage..."
php artisan storage:link

# 7. Build dos assets
Write-Log "7. Compilando assets para produção..."
if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm ci
    npm run build
    Write-Log "Assets compilados com sucesso"
} else {
    Write-Warning-Log "NPM não encontrado. Compile os assets manualmente: npm run build"
}

# 8. Limpeza de cache
Write-Log "8. Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 9. Recriar caches otimizados
Write-Log "9. Recriando caches otimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Verificações finais
Write-Log "10. Executando verificações finais..."

# Verificar se os diretórios necessários existem
Write-Log "    - Verificando estrutura de diretórios..."
New-Item -ItemType Directory -Force -Path "storage\app\public\relatorios" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\logs" | Out-Null

# Verificar permissões (no Windows isso é limitado)
Write-Log "    - Verificando permissões..."
if (-not (Test-Path "storage" -PathType Container)) {
    Write-Error-Log "Diretório storage não encontrado!"
}

# 11. Informações do sistema
Write-Log "11. Informações do sistema:"
$appName = (Select-String -Path ".env" -Pattern "APP_NAME=(.+)").Matches[0].Groups[1].Value
$version = (Select-String -Path ".env" -Pattern "APP_VERSION=(.+)").Matches[0].Groups[1].Value
$environment = (Select-String -Path ".env" -Pattern "APP_ENV=(.+)").Matches[0].Groups[1].Value
$database = (Select-String -Path ".env" -Pattern "DB_DATABASE=(.+)").Matches[0].Groups[1].Value
$username = (Select-String -Path ".env" -Pattern "DB_USERNAME=(.+)").Matches[0].Groups[1].Value

Write-Host "    📱 Nome: $appName" -ForegroundColor White
Write-Host "    🔢 Versão: $version" -ForegroundColor White
Write-Host "    🌍 Ambiente: $environment" -ForegroundColor White
Write-Host "    🗄️  Banco: $database" -ForegroundColor White
Write-Host "    👤 Usuário DB: $username" -ForegroundColor White

Write-Host ""
Write-Host "===========================================" -ForegroundColor Cyan
Write-Log "✅ DEPLOY CONCLUÍDO COM SUCESSO!"
Write-Host "===========================================" -ForegroundColor Cyan
Write-Host ""

Write-Info "📋 Próximos passos no CloudPanel:"
Write-Host "   1. Configure o domínio/subdomínio"
Write-Host "   2. Configure SSL/HTTPS"
Write-Host "   3. Ajuste as configurações de PHP (memória, uploads)"
Write-Host "   4. Configure backup automático"
Write-Host "   5. Teste todas as funcionalidades"
Write-Host ""

Write-Info "🔧 Comandos úteis para manutenção:"
Write-Host "   - Limpar cache: php artisan cache:clear"
Write-Host "   - Ver logs: Get-Content storage\logs\laravel.log -Tail 50"
Write-Host "   - Backup DB: mysqldump -u appuser -p relatodb > backup.sql"
Write-Host ""

Write-Warning-Log "⚠️  Lembre-se de:"
Write-Host "   - Configurar backups regulares"
Write-Host "   - Monitorar logs de erro"
Write-Host "   - Manter o sistema atualizado"
Write-Host ""

Write-Log "Deploy finalizado! Sistema pronto para produção. 🚀" 