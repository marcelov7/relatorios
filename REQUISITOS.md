# Requisitos para Executar o Sistema de Relatórios

## Requisitos de Sistema

### Backend
- Node.js (v14 ou superior)
- PostgreSQL (v12 ou superior)
- NPM ou Yarn

### Frontend
- Node.js (v14 ou superior)
- NPM ou Yarn

## Configuração do Ambiente

### 1. Banco de Dados
- Instalar PostgreSQL
- Criar um banco de dados chamado `relatorios_db`
- Configurar usuário e senha (padrão: `postgres` / `123456`)

### 2. Backend
- Navegar até a pasta `backend`
- Instalar dependências: `npm install`
- Configurar arquivo `.env` (usar `.env.example` como base)
- Executar migrações: `npx prisma migrate dev`
- Iniciar servidor: `npm run dev`

### 3. Frontend
- Navegar até a pasta `frontend`
- Instalar dependências: `npm install`
- Configurar arquivo `.env` (usar `.env.example` como base)
- Iniciar aplicação: `npm run dev`

## Solução de Problemas

### Erro de Conexão com Banco de Dados
- Verificar se o PostgreSQL está em execução
- Confirmar as credenciais no arquivo `.env`
- Verificar se o banco de dados `relatorios_db` existe

### Erro de Compilação TypeScript
- Verificar se todas as dependências foram instaladas
- Limpar cache: `npm cache clean --force`
- Reinstalar node_modules: `rm -rf node_modules && npm install`

### Erro de Autenticação
- Verificar se o JWT_SECRET está configurado no arquivo `.env`
- Confirmar se as credenciais de login estão corretas

## Comandos Úteis

### Backend
```bash
# Iniciar em modo desenvolvimento
npm run dev

# Compilar o projeto
npm run build

# Iniciar em modo produção
npm run start

# Executar migrações
npx prisma migrate dev

# Gerar client Prisma
npx prisma generate
```

### Frontend
```bash
# Iniciar em modo desenvolvimento
npm run dev

# Compilar o projeto
npm run build

# Iniciar em modo produção
npm run start
```