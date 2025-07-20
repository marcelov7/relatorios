# Credenciais de Teste - Sistema de Relatórios

## 🚀 Acesso Rápido ao Sistema

**URL de Login:** http://localhost:8000/login

---

## 👨‍💼 Usuários Administradores

### 1. Administrador Principal
- **Email:** `admin@sistema.com`
- **Senha:** `admin123`
- **Nome:** Administrador do Sistema
- **Setor:** Tecnologia da Informação
- **Cargo:** Administrador do Sistema

### 2. Administrador de Teste
- **Email:** `admin@teste.com`
- **Senha:** `admin123`
- **Nome:** Administrador de Teste
- **Setor:** Tecnologia da Informação
- **Cargo:** Administrador de Teste

### 3. Outros Administradores
- **Email:** `admin2@sistema.com`
- **Email:** `novo.admin@sistema.com`

---

## 👥 Usuários Comuns

### 1. João Silva
- **Email:** `joao.silva@empresa.com`
- **Senha:** `123456`
- **Setor:** Produção
- **Cargo:** Operador de Máquinas

### 2. Maria Santos
- **Email:** `maria.santos@empresa.com`
- **Senha:** `123456`
- **Setor:** Manutenção
- **Cargo:** Técnica em Manutenção

### 3. Pedro Oliveira
- **Email:** `pedro.oliveira@empresa.com`
- **Senha:** `123456`
- **Setor:** Qualidade
- **Cargo:** Analista de Qualidade

### 4. Ana Costa (Inativa)
- **Email:** `ana.costa@empresa.com`
- **Senha:** `123456`
- **Setor:** Logística
- **Cargo:** Coordenadora de Logística
- **Status:** Inativa

---

## 🛠️ Comandos Úteis

### Listar Administradores
```bash
php artisan list:admin-users
```

### Criar Novo Administrador
```bash
php artisan create:test-admin [email] [senha]
```

### Executar Seeder de Dados de Teste
```bash
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=TestRelatorioFiltersSeeder
```

### Testar Filtros de Relatórios
```bash
php artisan test:relatorio-filters
```

---

## 🔧 Funcionalidades Disponíveis

### Para Administradores:
- ✅ Gerenciar todos os relatórios
- ✅ Gerenciar usuários
- ✅ Gerenciar equipamentos
- ✅ Gerenciar setores
- ✅ Gerenciar locais
- ✅ Acessar todas as funcionalidades do sistema
- ✅ Visualizar relatórios de todos os usuários
- ✅ Editar/excluir qualquer relatório

### Para Usuários Comuns:
- ✅ Criar relatórios próprios
- ✅ Editar relatórios próprios (dentro do prazo)
- ✅ Visualizar relatórios próprios
- ✅ Filtrar relatórios por setor, autor, status, etc.
- ✅ Usar paginação configurável (12, 30, 60, 100 itens)

---

## 🎯 Testando as Melhorias Implementadas

### 1. Filtros de Relatórios
- Acesse: http://localhost:8000/relatorios
- Teste os filtros por:
  - **Setor** (dos equipamentos de teste)
  - **Autor** (quem criou o relatório)
  - **Status** (Aberta, Em Andamento, Concluída, Cancelada)
  - **Data de início/fim**

### 2. Paginação Configurável
- Na página de relatórios, use o seletor "Itens por página"
- Teste as opções: 12, 30, 60, 100 itens

### 3. Tema Dark
- O sistema suporta tema dark/light
- Teste a responsividade em diferentes dispositivos

---

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- 📱 **Mobile** (smartphones)
- 📱 **Tablet** (tablets)
- 💻 **Desktop** (computadores)

---

## 🔒 Segurança

- Todas as senhas são criptografadas
- Autenticação obrigatória para todas as páginas
- Controle de acesso baseado em roles
- Proteção CSRF ativa
- Validação de dados em todas as operações

---

## 🚨 Importante

- **Nunca use essas credenciais em produção**
- **Altere as senhas após o primeiro acesso**
- **Mantenha o sistema atualizado**
- **Faça backup regular dos dados**

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs em `storage/logs/`
2. Execute `php artisan migrate:status` para verificar migrações
3. Execute `php artisan config:cache` para limpar cache
4. Execute `php artisan route:clear` para limpar rotas 