# Deploy Sistema de Relatórios no CloudPanel

## 📋 Pré-requisitos

- CloudPanel instalado e configurado
- PHP 8.1+ com extensões: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 8.0+
- Node.js 18+ (para build dos assets)
- Redis (opcional, mas recomendado)

## 🚀 Passos para Deploy

### 1. Preparar o Ambiente

```bash
# No CloudPanel, criar novo site PHP
# Configurar domínio e SSL
# Configurar banco MySQL
```

### 2. Upload dos Arquivos

```bash
# Fazer upload de todos os arquivos do projeto
# Ou usar Git clone diretamente no servidor
git clone https://github.com/seu-usuario/sistema-relatorios.git
```

### 3. Configurar Permissões

```bash
# Ajustar permissões das pastas
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 4. Instalar Dependências

```bash
# Instalar dependências PHP
composer install --optimize-autoloader --no-dev

# Instalar dependências Node.js
npm install

# Build dos assets para produção
npm run build
```

### 5. Configurar Ambiente

```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_relatorios
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Configurar Banco de Dados

```bash
# Executar migrations
php artisan migrate --force

# Popular com dados de exemplo (opcional)
php artisan db:seed --class=RelatorioSeeder
```

### 7. Configurar Cache e Otimizações

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize
```

### 8. Configurar Web Server (Nginx)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔧 Configurações Específicas do CloudPanel

### 1. PHP Settings
```ini
; php.ini adjustments
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

### 2. Cron Jobs (Opcional)
```bash
# Adicionar no crontab para tarefas agendadas
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. SSL/HTTPS
```bash
# CloudPanel automaticamente configura SSL
# Certificar que APP_URL está com https://
APP_URL=https://yourdomain.com
```

## 📱 Funcionalidades Mobile/PWA

O sistema já está configurado com:
- ✅ Design responsivo mobile-first
- ✅ Manifest PWA (`/manifest.json`)
- ✅ Meta tags para mobile
- ✅ Pull-to-refresh
- ✅ Swipe actions nos cards
- ✅ Ícones para instalação como app

## 🔒 Segurança

### Headers de Segurança (nginx)
```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
add_header Referrer-Policy "strict-origin-when-cross-origin";
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'";
```

### Configurações do Laravel
```env
APP_DEBUG=false
APP_ENV=production
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

## 🔄 Atualizações Futuras

```bash
# Para atualizações:
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 Monitoramento

- Logs: `storage/logs/laravel.log`
- Performance: Use ferramentas como New Relic ou Blackfire
- Uptime: Configure monitoramento de uptime

## 🆘 Troubleshooting

### Erro 500
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
ls -la storage/
ls -la bootstrap/cache/
```

### Assets não carregam
```bash
# Rebuild assets
npm run build

# Verificar manifest
cat public/build/manifest.json
```

### Banco não conecta
```bash
# Testar conexão
php artisan tinker
DB::connection()->getPdo();
```

---

## 📞 Suporte

Para suporte técnico, verificar:
1. Logs da aplicação
2. Logs do servidor web
3. Logs do PHP-FPM
4. Status dos serviços (MySQL, Redis, etc.) 