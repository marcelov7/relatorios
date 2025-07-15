# 🚀 Deploy CloudPanel - Sistema de Relatórios V1.00

## 📋 Informações do Sistema

- **Nome**: Sistema de Relatórios
- **Versão**: V1.00
- **Framework**: Laravel 11
- **Banco de Dados**: MySQL
- **Frontend**: Vue.js 3 + Inertia.js
- **CSS**: Tailwind CSS

## 🗄️ Configurações do Banco de Dados

```
Usuário: appuser
Senha: M@rcelo1809@3033
Base de Dados: relatodb
```

## 📦 Pré-requisitos

### No CloudPanel:
- PHP 8.2 ou superior
- MySQL 8.0 ou superior
- Composer instalado
- Node.js 18+ e NPM
- Extensões PHP necessárias:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD ou Imagick

### Configurações PHP Recomendadas:
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

## 🔧 Processo de Deploy

### 1. Preparação do Ambiente

```bash
# 1. Clone/upload do projeto para o CloudPanel
# 2. Navegue até o diretório do projeto
cd /home/seu-usuario/htdocs/seu-dominio.com

# 3. Copie os arquivos do sistema para este diretório
```

### 2. Configuração do Banco de Dados

No CloudPanel, crie:
- **Banco de Dados**: `relatodb`
- **Usuário**: `appuser`
- **Senha**: `M@rcelo1809@3033`
- **Privilégios**: Todos os privilégios no banco `relatodb`

### 3. Configuração do .env

```bash
# Copie o arquivo de configuração
cp env-production.txt .env

# Edite o arquivo .env com suas configurações
nano .env
```

#### Configurações obrigatórias no .env:

```env
APP_NAME="Sistema de Relatórios"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=relatodb
DB_USERNAME=appuser
DB_PASSWORD="M@rcelo1809@3033"
```

### 4. Execução do Deploy

```bash
# Torne o script executável
chmod +x deploy.sh

# Execute o script de deploy
./deploy.sh
```

### 5. Configuração Manual (se necessário)

Se preferir executar manualmente:

```bash
# 1. Instalar dependências
composer install --optimize-autoloader --no-dev

# 2. Gerar chave da aplicação
php artisan key:generate --force

# 3. Configurar storage
php artisan storage:link

# 4. Executar migrações
php artisan migrate --force

# 5. Executar seeders (dados iniciais)
php artisan db:seed --force

# 6. Compilar assets
npm ci
npm run build

# 7. Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Configurar permissões
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
```

## ⚙️ Configurações do CloudPanel

### 1. Document Root
Aponte o document root para: `/public`

### 2. SSL/HTTPS
Configure certificado SSL para o domínio

### 3. PHP Configuration
Ajuste no painel PHP:
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

### 4. Cron Jobs (Opcional)
Se necessário, configure:
```bash
# A cada minuto para processamento de filas
* * * * * cd /path/to/artisan && php artisan schedule:run >> /dev/null 2>&1
```

## 🔐 Usuário Administrador

O sistema criará automaticamente um usuário administrador:

```
Email: admin@sistema.com
Senha: admin123
```

**⚠️ IMPORTANTE**: Altere a senha após o primeiro login!

## 📂 Estrutura de Diretórios

```
projeto/
├── app/                 # Lógica da aplicação
├── bootstrap/           # Inicialização do Laravel
├── config/             # Configurações
├── database/           # Migrações e seeders
├── public/             # Document root (assets públicos)
├── resources/          # Views, CSS, JS
├── routes/             # Definições de rotas
├── storage/            # Arquivos gerados (logs, uploads)
├── tests/              # Testes automatizados
├── vendor/             # Dependências Composer
├── .env                # Configurações de ambiente
└── artisan             # CLI do Laravel
```

## 🔍 Verificações Pós-Deploy

### 1. Teste de Conectividade
- Acesse: `https://seu-dominio.com`
- Verifique se a página de login carrega

### 2. Teste de Banco de Dados
```bash
php artisan migrate:status
```

### 3. Teste de Upload
- Faça login no sistema
- Tente criar um relatório com imagem
- Verifique se o upload funciona

### 4. Teste de Cache
```bash
php artisan cache:clear
php artisan config:cache
```

## 🛠️ Comandos de Manutenção

### Limpeza de Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Recriar Cache Otimizado
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup do Banco
```bash
mysqldump -u appuser -p relatodb > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Logs do Sistema
```bash
tail -f storage/logs/laravel.log
```

## 🔧 Troubleshooting

### Erro 500 - Internal Server Error
1. Verifique os logs: `storage/logs/laravel.log`
2. Verifique permissões: `chmod -R 755 storage bootstrap/cache`
3. Limpe o cache: `php artisan cache:clear`

### Erro de Banco de Dados
1. Verifique as credenciais no `.env`
2. Teste conexão: `php artisan migrate:status`
3. Verifique se o banco existe

### Imagens não carregam
1. Verifique: `php artisan storage:link`
2. Permissões: `chmod -R 775 storage/app/public`

### CSS/JS não carregam
1. Execute: `npm run build`
2. Verifique se os assets estão em `public/build/`

## 📊 Funcionalidades do Sistema

### ✅ Módulos Incluídos
- ✅ **Autenticação de Usuários**
- ✅ **Gestão de Relatórios**
- ✅ **Upload de Imagens**
- ✅ **Filtros Avançados** (Data, Local, Status)
- ✅ **Dashboard Interativo**
- ✅ **Gestão de Locais**
- ✅ **Gestão de Equipamentos**
- ✅ **Sistema de Permissões**
- ✅ **Responsividade Mobile**
- ✅ **Dark Theme**
- ✅ **Relatórios em Tempo Real**

### 📱 Características
- **Mobile-First Design**
- **Progressive Web App (PWA)**
- **Pull-to-Refresh**
- **Filtros Dinâmicos**
- **Upload Múltiplo de Imagens**
- **Visualização em Modal**
- **Controle de Progresso**
- **Sistema de Notificações**

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o deploy:

1. **Logs**: Sempre verifique primeiro os logs em `storage/logs/laravel.log`
2. **Documentação**: Consulte a documentação do Laravel
3. **CloudPanel**: Consulte a documentação oficial do CloudPanel

## 🔄 Atualizações Futuras

Para futuras atualizações:

1. **Backup**: Sempre faça backup do banco e arquivos
2. **Teste**: Execute em ambiente de homologação primeiro
3. **Deploy**: Use o script `deploy.sh` para atualizações
4. **Verificação**: Execute todos os testes pós-deploy

---

**Sistema de Relatórios V1.00** - Deploy CloudPanel
Preparado para produção com todas as otimizações necessárias. 