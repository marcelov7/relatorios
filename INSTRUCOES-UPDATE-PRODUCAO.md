# 🚀 Instruções para Atualização em Produção - Sistema de Motores

## 📋 Resumo da Atualização

**Objetivo:** Adicionar o sistema completo de gerenciamento de motores ao sistema existente de relatórios.

**Funcionalidades Adicionadas:**
- ✅ CRUD completo de motores
- ✅ Filtros avançados com suporte mobile
- ✅ Upload de fotos
- ✅ Interface responsiva
- ✅ Integração com sistema existente

## 🔄 Processo de Atualização

### **Opção 1: Script Automatizado (Recomendado)**

```bash
# No servidor de produção
cd /caminho/do/sistema
./update-motores.ps1
```

### **Opção 2: Comandos Manuais**

#### **1. Backup (OBRIGATÓRIO)**
```bash
# Backup do banco
mysqldump -u [usuario] -p [banco] > backup_motores_$(date +%Y%m%d_%H%M%S).sql

# Backup do .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
```

#### **2. Atualizar Código**
```bash
# Pull das alterações
git pull origin master

# Verificar se os arquivos foram atualizados
ls -la app/Models/Motor.php
ls -la app/Http/Controllers/MotorController.php
ls -la resources/js/Pages/Motores/
```

#### **3. Dependências**
```bash
# PHP
composer install --optimize-autoloader --no-dev

# Node.js
npm ci
npm run build
```

#### **4. Migrações**
```bash
# Verificar status
php artisan migrate:status

# Executar migrações
php artisan migrate --force
```

#### **5. Otimizações**
```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **6. Estrutura**
```bash
# Criar diretório para uploads
mkdir -p storage/app/public/motores
```

## 🧪 Testes Pós-Atualização

### **Testes Obrigatórios**
1. **Acesso básico:**
   - [ ] Acessar `/motores`
   - [ ] Verificar se a página carrega

2. **CRUD completo:**
   - [ ] Criar um motor de teste
   - [ ] Editar o motor criado
   - [ ] Visualizar detalhes
   - [ ] Excluir o motor

3. **Filtros:**
   - [ ] Busca por texto
   - [ ] Filtros básicos (local, armazenamento, reserva)
   - [ ] Filtros avançados (potência, rotação, etc.)

4. **Upload:**
   - [ ] Upload de foto
   - [ ] Verificar se a foto aparece

5. **Mobile:**
   - [ ] Testar em smartphone
   - [ ] Verificar responsividade

### **Testes de Integração**
- [ ] Menu de navegação funciona
- [ ] Sistema de relatórios continua funcionando
- [ ] Login/logout funcionam
- [ ] Dashboard não quebrou

## 📁 Arquivos Principais Adicionados

### **Backend**
- `app/Models/Motor.php` - Modelo do motor
- `app/Http/Controllers/MotorController.php` - Controller
- `database/migrations/2024_01_01_000000_create_motores_table.php`
- `database/migrations/2024_01_01_000001_create_relatorio_motor_table.php`
- `database/migrations/2025_07_17_204222_update_motores_table_structure.php`
- `database/migrations/2025_07_17_205503_update_motores_local_reserva_fields.php`

### **Frontend**
- `resources/js/Pages/Motores/Index.vue` - Listagem
- `resources/js/Pages/Motores/Create.vue` - Criação
- `resources/js/Pages/Motores/Edit.vue` - Edição
- `resources/js/Pages/Motores/Show.vue` - Visualização

### **Configurações**
- `routes/web.php` - Rotas adicionadas
- `resources/js/Layouts/AppLayout.vue` - Menu atualizado

## 🔧 Comandos de Diagnóstico

### **Verificar Status**
```bash
# Status das migrações
php artisan migrate:status

# Listar rotas
php artisan route:list | grep motores

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

## 🚨 Problemas Comuns e Soluções

### **Erro 500**
- Verificar logs: `storage/logs/laravel.log`
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
- Verificar se o link simbólico existe: `php artisan storage:link`

### **Filtros não funcionam**
- Limpar cache do navegador
- Verificar se o JavaScript foi compilado

## 📞 Suporte

### **Documentação**
- `CHECKLIST-UPDATE-MOTORES.md` - Checklist detalhado
- `update-motores.ps1` - Script de atualização
- `CHECKLIST-DEPLOY.md` - Documentação geral

### **Logs Importantes**
- `storage/logs/laravel.log` - Logs do Laravel
- `storage/logs/error.log` - Logs de erro (se configurado)

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

- [ ] Backup realizado
- [ ] Código atualizado
- [ ] Dependências instaladas
- [ ] Migrações executadas
- [ ] Caches otimizados
- [ ] Testes realizados
- [ ] Sistema funcionando
- [ ] Logs verificados

---

**🎉 Sistema de Motores Ativo!**

**URL de acesso:** `https://seu-dominio.com/motores`

**Funcionalidades disponíveis:**
- Listagem com filtros
- Criação de motores
- Edição de motores
- Visualização detalhada
- Upload de fotos
- Interface mobile otimizada

**Para suporte:** Verificar logs e documentação fornecida. 