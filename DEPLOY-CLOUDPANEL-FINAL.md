# 🚀 Deploy no CloudPanel - Filtro por Setor

## 📋 Resumo das Alterações

**Commit:** `36acac3` - feat: implementa filtro por setor dos equipamentos de teste

### ✅ **Principais Mudanças:**
- ❌ **Removido:** Filtro "Local" (locais físicos)
- ✅ **Adicionado:** Filtro "Setor" (setores dos equipamentos de teste)
- ✅ **Paginação:** Opções 12, 30, 60, 100 itens por página
- ✅ **Interface:** Mais limpa e organizada

---

## 🗄️ Estrutura do Banco

### **Tabela Principal:** `equipamento_tests`
- Campo `setor` VARCHAR(255) - **NOVO** - Fonte dos setores para o filtro
- Campo `tag` VARCHAR(255) UNIQUE - Identificador único
- Campo `nome` VARCHAR(255) - Nome do equipamento
- Campo `status` VARCHAR(255) - Status do equipamento

### **Setores Disponíveis:**
- **Laboratório** (2 equipamentos)
- **Logística** (2 equipamentos)
- **Manutenção** (6 equipamentos)
- **Produção** (6 equipamentos)
- **Qualidade** (2 equipamentos)
- **forno** (1 equipamento)

---

## 📁 Arquivos para Deploy

### **📄 Documentação:**
- `ALTERACOES-CLOUDPANEL.md` - Lista completa de alterações
- `MUDANCAS-FILTRO-SETOR.md` - Documentação das mudanças
- `DEPLOY-CLOUDPANEL-FINAL.md` - Este arquivo

### **🔧 Scripts de Deploy:**
- `scripts/deploy-cloudpanel.sh` - **SCRIPT PRINCIPAL** - Deploy automatizado
- `scripts/check_and_add_setor_column.php` - Verifica e adiciona coluna setor
- `scripts/add_setor_column.sql` - Script SQL para adicionar coluna

### **⚙️ Comandos Artisan:**
- `app/Console/Commands/TestSetoresEquipamentoTest.php` - Testa setores
- `app/Console/Commands/CreateTestAdmin.php` - Cria admin
- `app/Console/Commands/ListAdminUsers.php` - Lista admins

### **🌱 Seeders:**
- `database/seeders/EquipamentoTestSeeder.php` - Cria equipamentos de teste
- `database/seeders/TestRelatorioFiltersSeeder.php` - Cria dados de teste

---

## 🚀 Como Fazer o Deploy

### **Opção 1: Script Automatizado (RECOMENDADO)**

```bash
# 1. Acessar o servidor via SSH
ssh usuario@seu-servidor.com

# 2. Navegar para o diretório do projeto
cd /home/cloudpanel/htdocs/seu-dominio.com

# 3. Executar o script de deploy
./scripts/deploy-cloudpanel.sh
```

### **Opção 2: Deploy Manual**

```bash
# 1. Backup do banco (OBRIGATÓRIO)
mysqldump -u [usuario] -p [banco] > backup_antes_deploy.sql

# 2. Atualizar código
git pull origin master

# 3. Instalar dependências
composer install --no-dev --optimize-autoloader

# 4. Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 5. Executar migrations
php artisan migrate --force

# 6. Verificar estrutura da tabela
php scripts/check_and_add_setor_column.php

# 7. Executar seeders (opcional)
php artisan db:seed --class=EquipamentoTestSeeder

# 8. Testar funcionamento
php artisan test:setores-equipamento-test
```

---

## 🔧 Script para Adicionar Coluna

### **Se a coluna `setor` não existir, execute:**

```sql
-- Script SQL para adicionar coluna setor
ALTER TABLE equipamento_tests ADD COLUMN setor VARCHAR(255) NULL AFTER nome;
```

### **Ou use o script PHP:**

```bash
php scripts/check_and_add_setor_column.php
```

---

## ⚠️ Pontos de Atenção

### **🔍 Verificações Importantes:**
1. **Backup do banco** antes de qualquer alteração
2. **Permissões** de arquivos no servidor
3. **Arquivo .env** configurado corretamente
4. **Dependências** do Composer instaladas

### **🐛 Possíveis Problemas:**
1. **Cache antigo** - Limpar todos os caches
2. **Permissões** - Verificar permissões de arquivos
3. **Migrations** - Executar migrations pendentes
4. **Coluna setor** - Verificar se existe na tabela

---

## 🧪 Como Testar

### **1. Verificar Setores:**
```bash
php artisan test:setores-equipamento-test
```

### **2. Criar Admin de Teste:**
```bash
php artisan create:test-admin
```

### **3. Listar Admins:**
```bash
php artisan list:admin-users
```

### **4. Testar Interface:**
1. Acesse a página de relatórios
2. Use o filtro "Setor"
3. Teste a paginação (12, 30, 60, 100)
4. Verifique se os setores aparecem corretamente

---

## 📞 Comandos de Debug

### **Verificar Estrutura:**
```bash
php artisan check:table-structure
```

### **Testar Filtros:**
```bash
php artisan test:relatorio-filters
```

### **Verificar Logs:**
```bash
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist de Deploy

- [ ] Backup do banco realizado
- [ ] Código atualizado via Git
- [ ] Dependências instaladas
- [ ] Caches limpos
- [ ] Migrations executadas
- [ ] Coluna setor verificada/criada
- [ ] Seeders executados (se necessário)
- [ ] Testes realizados
- [ ] Funcionalidades verificadas
- [ ] Logs verificados

---

## 🎯 Resultado Esperado

Após o deploy, o sistema deve ter:

### **✅ Funcionalidades:**
- Filtro "Setor" funcionando na página de relatórios
- Paginação com opções 12, 30, 60, 100 itens
- Setores únicos dos equipamentos de teste
- Interface mais limpa e organizada

### **✅ Setores Disponíveis:**
- Laboratório
- Logística
- Manutenção
- Produção
- Qualidade
- forno

### **✅ Comandos Disponíveis:**
- `php artisan test:setores-equipamento-test`
- `php artisan create:test-admin`
- `php artisan list:admin-users`

---

## 📞 Suporte

### **Em caso de problemas:**
1. Verificar logs em `storage/logs/laravel.log`
2. Executar comandos de debug
3. Verificar estrutura da tabela
4. Testar funcionalidades uma por uma

### **Arquivos de Ajuda:**
- `ALTERACOES-CLOUDPANEL.md` - Lista completa de alterações
- `MUDANCAS-FILTRO-SETOR.md` - Documentação detalhada
- `scripts/check_and_add_setor_column.php` - Script de verificação

---

## 🎉 Deploy Concluído!

O sistema agora tem o filtro por setor funcionando exatamente como solicitado:
- ✅ Puxa setores dos equipamentos de teste
- ✅ Agrupa setores com mesmo nome
- ✅ Interface mais limpa e organizada
- ✅ Paginação melhorada
- ✅ Comandos de teste disponíveis 