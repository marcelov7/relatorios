# Correção de Permissões no CloudPanel

## Problema
Erro de permissão negada ao tentar escrever no arquivo de log e criar diretórios no storage:
```
The stream or file "/home/devaxis-app/htdocs/app.devaxis.com.br/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
```

## Solução

### Opção 1: Via SSH (Recomendado)

1. **Conecte via SSH ao servidor**
2. **Execute o script como root:**
   ```bash
   sudo bash /home/devaxis-app/htdocs/app.devaxis.com.br/fix-permissions.sh
   ```

### Opção 2: Via CloudPanel File Manager

1. **Acesse o CloudPanel**
2. **Vá para File Manager**
3. **Navegue até a pasta do projeto**
4. **Execute via Terminal do CloudPanel:**
   ```bash
   cd /home/devaxis-app/htdocs/app.devaxis.com.br
   bash fix-permissions-user.sh
   ```

### Opção 3: Comandos Manuais

Se os scripts não funcionarem, execute estes comandos manualmente:

```bash
# Como root ou com sudo
cd /home/devaxis-app/htdocs/app.devaxis.com.br

# Corrigir proprietário
chown -R devaxis-app:devaxis-app .

# Permissões para diretórios
find . -type d -exec chmod 755 {} \;

# Permissões para arquivos
find . -type f -exec chmod 644 {} \;

# Permissões especiais para storage
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Criar diretórios necessários
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Criar arquivo de log
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# Link simbólico do storage
php artisan storage:link

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Opção 4: Via CloudPanel Interface

1. **Acesse o CloudPanel**
2. **Vá para Domains > app.devaxis.com.br**
3. **Clique em "File Manager"**
4. **Navegue até a pasta storage/**
5. **Clique com botão direito > Permissions**
6. **Defina:**
   - Owner: devaxis-app
   - Group: devaxis-app
   - Permissions: 775 (para pastas) / 664 (para arquivos)

## Verificação

Após executar a correção, verifique se:

1. **O arquivo de log pode ser criado:**
   ```bash
   touch /home/devaxis-app/htdocs/app.devaxis.com.br/storage/logs/test.log
   ```

2. **O upload de imagens funciona:**
   - Tente fazer upload de uma imagem no sistema

3. **Os logs não mostram mais erros de permissão**

## Estrutura de Permissões Correta

```
/home/devaxis-app/htdocs/app.devaxis.com.br/
├── storage/ (775)
│   ├── logs/ (775)
│   │   └── laravel.log (664)
│   ├── app/ (775)
│   │   └── public/ (775)
│   └── framework/ (775)
│       ├── cache/ (775)
│       ├── sessions/ (775)
│       └── views/ (775)
├── bootstrap/ (755)
│   └── cache/ (775)
└── public/ (755)
    └── storage -> ../storage/app/public (link simbólico)
```

## Troubleshooting

### Se ainda houver problemas:

1. **Verificar usuário do servidor web:**
   ```bash
   ps aux | grep nginx
   ps aux | grep apache
   ```

2. **Verificar permissões atuais:**
   ```bash
   ls -la /home/devaxis-app/htdocs/app.devaxis.com.br/storage/
   ```

3. **Verificar logs do servidor:**
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f /var/log/apache2/error.log
   ```

### Contato com Suporte

Se o problema persistir, forneça ao suporte:
- Logs de erro completos
- Saída do comando `ls -la` na pasta storage
- Informações do usuário do servidor web 