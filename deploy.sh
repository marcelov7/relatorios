#!/bin/bash

# Script de Deploy para CloudPanel - Sistema de Relatórios V1.00
# Autor: Sistema de Relatórios
# Data: $(date)

echo "===========================================" 
echo "📦 DEPLOY - Sistema de Relatórios V1.00" 
echo "🌐 CloudPanel - Configuração MySQL"
echo "==========================================="

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Arquivo artisan não encontrado. Execute este script no diretório raiz do Laravel."
    exit 1
fi

log "Iniciando processo de deploy..."

# 1. Backup das configurações atuais
log "1. Criando backup das configurações..."
if [ -f ".env" ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    log "Backup do .env criado"
fi

# 2. Configurar .env para produção
log "2. Configurando arquivo .env..."
if [ ! -f ".env" ]; then
    if [ -f "env-production.txt" ]; then
        cp env-production.txt .env
        log "Arquivo .env criado baseado em env-production.txt"
        warning "⚠️  IMPORTANTE: Ajuste as configurações no .env antes de continuar!"
        warning "   - APP_URL: https://seu-dominio.com"
        warning "   - APP_KEY: Execute 'php artisan key:generate'"
        warning "   - Configurações de email se necessário"
        echo ""
        read -p "Pressione ENTER após ajustar o .env..."
    else
        error "Arquivo env-production.txt não encontrado!"
        exit 1
    fi
fi

# 3. Gerar APP_KEY se necessário
log "3. Verificando APP_KEY..."
if ! grep -q "APP_KEY=base64:" .env; then
    log "Gerando nova APP_KEY..."
    php artisan key:generate --force
fi

# 4. Otimizações para produção
log "4. Aplicando otimizações para produção..."

# Cache de configuração
log "   - Cache de configuração..."
php artisan config:cache

# Cache de rotas
log "   - Cache de rotas..."
php artisan route:cache

# Cache de views
log "   - Cache de views..."
php artisan view:cache

# Autoload otimizado
log "   - Otimizando autoload..."
composer install --optimize-autoloader --no-dev

# 5. Configurações de banco de dados
log "5. Configurando banco de dados..."
log "   - Testando conexão com o banco..."
php artisan migrate:status

read -p "Executar migrações do banco de dados? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log "   - Executando migrações..."
    php artisan migrate --force
    
    read -p "Executar seeders (dados iniciais)? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        log "   - Executando seeders..."
        php artisan db:seed --force
    fi
fi

# 6. Configurar storage e permissões
log "6. Configurando storage e permissões..."
php artisan storage:link

# Configurar permissões (CloudPanel)
log "   - Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# 7. Build dos assets
log "7. Compilando assets para produção..."
if command -v npm &> /dev/null; then
    npm ci
    npm run build
    log "Assets compilados com sucesso"
else
    warning "NPM não encontrado. Compile os assets manualmente: npm run build"
fi

# 8. Limpeza de cache
log "8. Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 9. Recriar caches otimizados
log "9. Recriando caches otimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Verificações finais
log "10. Executando verificações finais..."

# Verificar se os diretórios necessários existem
log "    - Verificando estrutura de diretórios..."
mkdir -p storage/app/public/relatorios
mkdir -p storage/logs

# Verificar permissões
log "    - Verificando permissões..."
if [ ! -w "storage" ]; then
    error "Diretório storage não tem permissão de escrita!"
fi

if [ ! -w "bootstrap/cache" ]; then
    error "Diretório bootstrap/cache não tem permissão de escrita!"
fi

# 11. Informações do sistema
log "11. Informações do sistema:"
echo "    📱 Nome: $(grep APP_NAME .env | cut -d'=' -f2)"
echo "    🔢 Versão: $(grep APP_VERSION .env | cut -d'=' -f2)"
echo "    🌍 Ambiente: $(grep APP_ENV .env | cut -d'=' -f2)"
echo "    🗄️  Banco: $(grep DB_DATABASE .env | cut -d'=' -f2)"
echo "    👤 Usuário DB: $(grep DB_USERNAME .env | cut -d'=' -f2)"

echo ""
echo "==========================================="
log "✅ DEPLOY CONCLUÍDO COM SUCESSO!"
echo "==========================================="
echo ""
info "📋 Próximos passos no CloudPanel:"
echo "   1. Configure o domínio/subdomínio"
echo "   2. Configure SSL/HTTPS"
echo "   3. Ajuste as configurações de PHP (memória, uploads)"
echo "   4. Configure backup automático"
echo "   5. Teste todas as funcionalidades"
echo ""
info "🔧 Comandos úteis para manutenção:"
echo "   - Limpar cache: php artisan cache:clear"
echo "   - Ver logs: tail -f storage/logs/laravel.log"
echo "   - Backup DB: mysqldump -u appuser -p relatodb > backup.sql"
echo ""
warning "⚠️  Lembre-se de:"
echo "   - Configurar backups regulares"
echo "   - Monitorar logs de erro"
echo "   - Manter o sistema atualizado"
echo ""
log "Deploy finalizado! Sistema pronto para produção. 🚀" 