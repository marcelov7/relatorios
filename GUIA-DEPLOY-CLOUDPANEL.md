# 🚀 Guia Rápido - Deploy no CloudPanel

## 📋 Pré-requisitos

### **1. Acesso SSH configurado**
- Chave SSH configurada no servidor
- Acesso ao terminal do CloudPanel
- Permissões de escrita no diretório do projeto

### **2. Informações necessárias**
- **Usuário SSH:** (ex: cloudpanel)
- **Host/IP:** (ex: 192.168.1.100 ou seu-dominio.com)
- **Caminho do projeto:** (ex: /home/cloudpanel/htdocs/seu-dominio.com)
- **Domínio:** (ex: seu-dominio.com)

## 🔄 Opções de Deploy

### **Opção 1: Script Automatizado (Recomendado)**

#### **Windows (PowerShell)**
```powershell
# No diretório do projeto
.\deploy-cloudpanel-simple.ps1
```

#### **Linux/Mac (Bash)**
```bash
# No diretório do projeto
chmod +x deploy-cloudpanel.sh
./deploy-cloudpanel.sh
```

### **Opção 2: Comandos Manuais**

#### **1. Conectar ao servidor**
```bash
ssh usuario@host-do-servidor
```

#### **2. Navegar para o projeto**
```bash
cd /caminho/do/projeto
```

#### **3. Backup do banco (OBRIGATÓRIO)**
```bash
mysqldump -u appuser -p'M@rcelo1809@3033' relatodb > backup_motores_$(date +%Y%m%d_%H%M%S).sql
```

#### **4. Atualizar código**
```bash
git fetch origin
git reset --hard origin/master
git clean -fd
```

#### **5. Configurar ambiente**
```bash
cp env-production.txt .env
sed -i 's|https://seu-dominio.com|https://SEU-DOMINIO-REAL.com|g' .env
php artisan key:generate --force
```

#### **6. Instalar dependências**
```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

#### **7. Executar migrações**
```bash
php artisan migrate --force
```

#### **8. Otimizações**
```bash
php artisan storage:link
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **9. Permissões**
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
mkdir -p storage/app/public/motores
```

## 🧪 Testes Pós-Deploy

### **Testes Obrigatórios**
1. **Acesso básico:**
   - [ ] `https://seu-dominio.com` - Página principal carrega
   - [ ] `https://seu-dominio.com/motores` - Sistema de motores acessível

2. **Login e funcionalidades:**
   - [ ] Login: `admin@sistema.com` / `admin123`
   - [ ] Dashboard funciona
   - [ ] Sistema de relatórios continua funcionando

3. **Sistema de motores:**
   - [ ] Criar um motor de teste
   - [ ] Editar o motor criado
   - [ ] Upload de foto funciona
   - [ ] Filtros funcionam
   - [ ] Responsividade mobile

## 🔧 Comandos de Diagnóstico

### **Verificar Status**
```bash
# Status das migrações
php artisan migrate:status

# Verificar arquivos
ls -la app/Models/Motor.php
ls -la app/Http/Controllers/MotorController.php
ls -la resources/js/Pages/Motores/

# Ver logs
tail -f storage/logs/laravel.log
```

### **Em Caso de Problemas**
```bash
# Limpar tudo
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permissões
chmod -R 755 storage bootstrap/cache
```

## 🚨 Problemas Comuns

### **Erro 500**
- Verificar logs: `tail -f storage/logs/laravel.log`
- Verificar permissões do storage
- Limpar caches

### **Página não encontrada**
- Verificar se as rotas foram cacheadas
- Executar: `php artisan route:clear && php artisan route:cache`

### **CSS/JS não carrega**
- Executar: `npm run build`
- Verificar se o diretório `public/build` existe

### **Upload não funciona**
- Verificar permissões: `chmod -R 775 storage/app/public`
- Verificar link simbólico: `php artisan storage:link`

## 📞 Suporte

### **Logs Importantes**
- `storage/logs/laravel.log` - Logs do Laravel
- `/var/log/apache2/error.log` - Logs do Apache (se aplicável)
- `/var/log/nginx/error.log` - Logs do Nginx (se aplicável)

### **Comandos Úteis**
```bash
# Verificar versão
php artisan --version

# Verificar ambiente
php artisan env

# Testar conexão com banco
php artisan tinker
>>> DB::connection()->getPdo();
```

## ✅ Checklist Final

- [ ] Backup do banco realizado
- [ ] Código atualizado via Git
- [ ] Dependências instaladas
- [ ] Migrações executadas
- [ ] Caches otimizados
- [ ] Permissões configuradas
- [ ] Testes realizados
- [ ] Sistema funcionando
- [ ] Logs verificados

---

**🎉 Sistema de Motores Ativo no CloudPanel!**

**URL de acesso:** `https://seu-dominio.com/motores`

**Funcionalidades disponíveis:**
- ✅ CRUD completo de motores
- ✅ Filtros avançados
- ✅ Upload de fotos
- ✅ Interface mobile otimizada
- ✅ Integração com sistema existente

**Para suporte:** Verificar logs e documentação fornecida. 