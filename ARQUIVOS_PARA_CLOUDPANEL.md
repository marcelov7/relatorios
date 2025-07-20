# 📁 ARQUIVOS PARA ATUALIZAR NO CLOUDPANEL

## 🚨 ERRO DE DEPLOY IDENTIFICADO

**Problema:** A migração `2025_07_17_205503_update_motores_local_reserva_fields` está falhando porque tenta modificar a coluna `reserva_almox` que não existe na tabela.

**Erro:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'reserva_almox' in 'motores'`

## 🔧 SOLUÇÃO PARA O ERRO DE DEPLOY

### **OPÇÃO 1: Script de Correção Automática (RECOMENDADO)**

Execute este script no CloudPanel para resolver o problema:

```bash
# No diretório do projeto no CloudPanel
php fix_deploy_error.php
```

### **OPÇÃO 2: Comandos Manuais**

Se preferir executar manualmente:

```bash
# 1. Limpar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Recarregar autoload
composer dump-autoload

# 3. Marcar migrações problemáticas como executadas
php artisan tinker --execute="DB::table('migrations')->insert(['migration' => '2025_07_17_205503_update_motores_local_reserva_fields', 'batch' => 5]);"

# 4. Executar nova migração de correção
php artisan migrate --path=database/migrations/2025_07_17_232924_fix_motores_table_complete.php
```

## 🔧 ARQUIVOS MODIFICADOS/CORRIGIDOS

### 1. **Modelo Motor** (CRÍTICO - CORRIGIDO)
```
app/Models/Motor.php
```
- ✅ Estrutura exata conforme definido pelo usuário
- ✅ `carcaca_fabricante` (campo único)
- ✅ `corrente_placa` (não corrente_nominal)
- ✅ `reserva_almox` (campo texto)
- ✅ `local` (campo texto)
- ✅ `armazenamento` (campo texto)

### 2. **Controller Motor** (CRÍTICO - CORRIGIDO)
```
app/Http/Controllers/MotorController.php
```
- ✅ Estrutura exata conforme definido pelo usuário
- ✅ Usa `carcaca_fabricante` (campo único)
- ✅ Usa `corrente_placa` (não corrente_nominal)
- ✅ Usa `reserva_almox` (campo texto)
- ✅ Usa `local` (campo texto)
- ✅ Usa `armazenamento` (campo texto)

### 3. **Rotas** (MANTIDO)
```
routes/web.php
```
- ✅ Mantida rota API: `motores-almoxarifado`

### 4. **Nova Migração de Correção** (CRÍTICO - NOVA)
```
database/migrations/2025_07_17_232924_fix_motores_table_complete.php
```
- ✅ Remove campos incorretos: `carcaca`, `corrente_nominal`, `local_id`
- ✅ Adiciona campos corretos: `carcaca_fabricante`, `corrente_placa`, `reserva_almox`, `local`, `armazenamento`
- ✅ Trata erros de foreign keys

### 5. **Script de Correção** (NOVO)
```
fix_deploy_error.php
```
- ✅ Resolve automaticamente o erro de deploy
- ✅ Corrige estrutura da tabela
- ✅ Marca migrações como executadas

## 🗄️ ESTRUTURA CORRETA DA TABELA

### Campos que devem existir na tabela `motores` (conforme definido pelo usuário):
```
- id (bigint, chave primária)
- tag (varchar, único)
- equipamento (varchar)
- carcaca_fabricante (varchar, nullable) ✅
- potencia_kw (decimal)
- potencia_cv (decimal)
- rotacao (int)
- corrente_placa (decimal, nullable) ✅
- corrente_configurada (decimal)
- tipo_equipamento_modelo (enum)
- fabricante (varchar, nullable) ✅
- reserva_almox (varchar, nullable) ✅
- local (varchar, nullable) ✅
- armazenamento (varchar, nullable) ✅
- foto (varchar)
- observacoes (text)
- ativo (boolean)
- created_at, updated_at (timestamps)
```

### Campos que NÃO devem existir:
```
- carcaca (separada) ❌
- corrente_nominal ❌
- local_id (chave estrangeira) ❌
```

## 🎨 FRONTEND (Vue.js) - JÁ EXISTE

### Páginas de Motores (TODAS EXISTEM):
```
resources/js/Pages/Motores/
├── Index.vue          (31KB - Listagem com filtros) ✅
├── Create.vue         (17KB - Formulário de criação) ✅
├── Edit.vue           (18KB - Formulário de edição) ✅
└── Show.vue           (17KB - Visualização detalhada) ✅
```

### Componentes (TODOS EXISTEM):
```
resources/js/Components/
├── AjaxPagination.vue (7.1KB - Paginação HTMX) ✅
├── Modal.vue          (3.3KB) ✅
├── Pagination.vue     (7.4KB) ✅
├── NotificationToast.vue (8.6KB) ✅
├── ConfirmDialog.vue  (9.9KB) ✅
└── [outros componentes] ✅
```

## 📋 RESUMO FINAL - O QUE ATUALIZAR

### 🎯 **ARQUIVOS CRÍTICOS PARA ATUALIZAR:**

1. `app/Models/Motor.php` - **CORREÇÃO CRÍTICA**
2. `app/Http/Controllers/MotorController.php` - **CORREÇÃO CRÍTICA**
3. `routes/web.php` - **MANTIDO COMO ESTAVA**
4. `database/migrations/2025_07_17_232924_fix_motores_table_complete.php` - **NOVA MIGRAÇÃO**
5. `fix_deploy_error.php` - **SCRIPT DE CORREÇÃO**

### ✅ **TODOS OS OUTROS ARQUIVOS JÁ EXISTEM E ESTÃO CORRETOS:**
- Páginas Vue.js ✅
- Componentes ✅
- Outras rotas ✅

## 🚀 COMANDOS PARA EXECUTAR NO CLOUDPANEL

### **PASSO 1: Atualizar arquivos**
Subir os 5 arquivos críticos para o CloudPanel.

### **PASSO 2: Executar script de correção**
```bash
# No diretório do projeto
php fix_deploy_error.php
```

### **PASSO 3: Limpar caches**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## 🔍 VERIFICAÇÃO PÓS-DEPLOY

1. Acessar `/motores` - deve carregar a listagem sem erro 500
2. Testar criação de motor
3. Testar filtros (busca, local, reserva_almox, fabricante, carcaca_fabricante)
4. Verificar se não há erros 500

## 📝 NOTAS IMPORTANTES

- **carcaca_fabricante**: Campo único (carcaça + fabricante) ✅
- **corrente_placa**: Campo decimal (não corrente_nominal) ✅
- **reserva_almox**: Campo texto ✅
- **local**: Campo texto (não chave estrangeira) ✅
- **armazenamento**: Campo texto ✅

## 🎯 PRIORIDADE DE ATUALIZAÇÃO

### **ALTA PRIORIDADE (5 ARQUIVOS NECESSÁRIOS):**
1. `app/Models/Motor.php` - **CORREÇÃO CRÍTICA**
2. `app/Http/Controllers/MotorController.php` - **CORREÇÃO CRÍTICA**
3. `routes/web.php` - **MANTIDO COMO ESTAVA**
4. `database/migrations/2025_07_17_232924_fix_motores_table_complete.php` - **NOVA MIGRAÇÃO**
5. `fix_deploy_error.php` - **SCRIPT DE CORREÇÃO**

### **NÃO PRECISA ATUALIZAR:**
- Frontend Vue.js (já existe e está correto)
- Componentes (já existem)

## 💡 DICA IMPORTANTE

**O erro de deploy acontece porque a migração antiga está tentando ser executada novamente. A solução é:**

1. **Executar o script `fix_deploy_error.php`** - isso resolve automaticamente
2. **Ou marcar manualmente as migrações como executadas**
3. **Executar a nova migração de correção**

Isso deve resolver o erro 500 e fazer o sistema funcionar corretamente com a estrutura exata que você definiu. 

## 🔧 **Solução para Foreign Key Constraint**

O erro indica que o índice `local_2` está sendo usado em uma **foreign key constraint**. Vamos resolver isso:

### **Passo 1: Identificar a Foreign Key**

Execute este comando no phpMyAdmin:
```sql
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'relatodb' 
AND TABLE_NAME = 'motores' 
AND COLUMN_NAME = 'local'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### **Opção 1: Script PHP (Recomendado)**
1. Edite o arquivo `fix_foreign_key_local.php` com suas credenciais
2. Execute:
```bash
php fix_foreign_key_local.php
```

### **Passo 2: Remover a Foreign Key**

Depois de identificar o nome da constraint, execute:
```sql
ALTER TABLE motores DROP FOREIGN KEY nome_da_constraint;
```

### **Passo 3: Remover o Índice**

Depois remova o índice:
```sql
ALTER TABLE motores DROP INDEX local_2;
```

### **Passo 4: Verificar**

```sql
<code_block_to_apply_changes_from>
```

## ⚠️ **Importante:**

- **Foreign keys** são restrições que ligam tabelas
- A coluna `local` provavelmente está referenciando outra tabela (como `locais`)
- Remover a foreign key **não afeta os dados**, apenas remove a restrição

##  **Sequência Completa:**

1. **Identificar** a foreign key
2. **Remover** a foreign key: `DROP FOREIGN KEY nome_constraint`
3. **Remover** o índice: `DROP INDEX local_2`
4. **Verificar** se foi removido
5. **Testar** o sistema

**Execute primeiro o comando para identificar a foreign key** e me informe o resultado! 🚀 