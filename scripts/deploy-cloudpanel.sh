#!/bin/bash

# =====================================================
# Script de Deploy para CloudPanel
# Sistema de Relatórios - Filtro por Setor
# =====================================================

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

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
}

info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] INFO: $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Este script deve ser executado no diretório raiz do Laravel"
    exit 1
fi

log "🚀 Iniciando deploy do Sistema de Relatórios no CloudPanel"

# 1. Backup do banco (se possível)
log "📦 Fazendo backup do banco de dados..."
if command -v mysqldump &> /dev/null; then
    # Ajuste estas variáveis conforme seu ambiente
    DB_NAME="sistema_relatorios"
    DB_USER="root"
    DB_PASS=""
    
    BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
    if mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
        log "✅ Backup criado: $BACKUP_FILE"
    else
        warn "⚠️  Não foi possível criar backup automático"
        warn "   Execute manualmente: mysqldump -u [usuario] -p [banco] > backup.sql"
    fi
else
    warn "⚠️  mysqldump não encontrado. Backup manual necessário."
fi

# 2. Atualizar código do Git
log "📥 Atualizando código do Git..."
if git pull origin master; then
    log "✅ Código atualizado com sucesso"
else
    error "❌ Falha ao atualizar código do Git"
    exit 1
fi

# 3. Instalar dependências
log "📦 Instalando dependências do Composer..."
if composer install --no-dev --optimize-autoloader; then
    log "✅ Dependências instaladas"
else
    error "❌ Falha ao instalar dependências"
    exit 1
fi

# 4. Limpar caches
log "🧹 Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
log "✅ Caches limpos"

# 5. Executar migrations
log "🗄️  Executando migrations..."
if php artisan migrate --force; then
    log "✅ Migrations executadas"
else
    error "❌ Falha ao executar migrations"
    exit 1
fi

# 6. Verificar e adicionar coluna setor se necessário
log "🔍 Verificando estrutura da tabela equipamento_tests..."
if php scripts/check_and_add_setor_column.php; then
    log "✅ Estrutura da tabela verificada"
else
    warn "⚠️  Erro ao verificar estrutura da tabela"
fi

# 7. Executar seeders (opcional)
read -p "Deseja executar os seeders para criar dados de teste? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log "🌱 Executando seeders..."
    php artisan db:seed --class=EquipamentoTestSeeder
    php artisan db:seed --class=TestRelatorioFiltersSeeder
    log "✅ Seeders executados"
fi

# 8. Verificar funcionamento
log "🔍 Verificando funcionamento..."
if php artisan test:setores-equipamento-test; then
    log "✅ Teste de setores executado com sucesso"
else
    warn "⚠️  Erro no teste de setores"
fi

# 9. Ajustar permissões
log "🔐 Ajustando permissões..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
log "✅ Permissões ajustadas"

# 10. Verificar arquivo .env
log "⚙️  Verificando configurações..."
if [ ! -f ".env" ]; then
    error "❌ Arquivo .env não encontrado!"
    warn "   Copie o arquivo .env.example para .env e configure as variáveis"
else
    log "✅ Arquivo .env encontrado"
fi

# 11. Gerar chave da aplicação se necessário
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    log "🔑 Gerando chave da aplicação..."
    php artisan key:generate
    log "✅ Chave da aplicação gerada"
fi

# 12. Verificar logs
log "📋 Verificando logs..."
if [ -f "storage/logs/laravel.log" ]; then
    log "✅ Logs disponíveis em storage/logs/laravel.log"
else
    warn "⚠️  Arquivo de log não encontrado"
fi

# Resumo final
log "🎉 Deploy concluído com sucesso!"
echo
echo "📋 Resumo das alterações:"
echo "   ✅ Filtro 'Local' substituído por 'Setor'"
echo "   ✅ Paginação com opções 12, 30, 60, 100 itens"
echo "   ✅ Setores únicos dos equipamentos de teste"
echo "   ✅ Interface mais limpa e organizada"
echo
echo "🔍 Para testar:"
echo "   1. Acesse a página de relatórios"
echo "   2. Use o filtro 'Setor'"
echo "   3. Teste a paginação"
echo
echo "📞 Comandos úteis:"
echo "   php artisan test:setores-equipamento-test"
echo "   php artisan create:test-admin"
echo "   php artisan list:admin-users"
echo
echo "📁 Arquivos importantes:"
echo "   ALTERACOES-CLOUDPANEL.md - Lista completa de alterações"
echo "   MUDANCAS-FILTRO-SETOR.md - Documentação das mudanças"
echo "   scripts/check_and_add_setor_column.php - Script de verificação"
echo

# Verificar se há erros nos logs recentes
if [ -f "storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(tail -n 50 storage/logs/laravel.log | grep -c "ERROR\|FATAL\|Exception")
    if [ $ERROR_COUNT -gt 0 ]; then
        warn "⚠️  Encontrados $ERROR_COUNT erros nos logs recentes"
        echo "   Últimas linhas do log:"
        tail -n 10 storage/logs/laravel.log
    else
        log "✅ Nenhum erro encontrado nos logs recentes"
    fi
fi

log "🚀 Sistema pronto para uso!" 