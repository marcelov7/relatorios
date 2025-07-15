# Sistema de Relatórios

Sistema web completo para gestão de relatórios técnicos, equipamentos, setores e usuários. Desenvolvido em **Laravel** (backend/API), **Vue 3 + Inertia.js** (frontend SPA), **TailwindCSS** (UI) e banco SQLite/MySQL.

## Funcionalidades
- Cadastro e gestão de relatórios técnicos
- Upload e galeria de imagens
- Seleção de equipamentos e setores
- Dashboard com estatísticas
- Controle de usuários e permissões (admin e comum)
- Tema escuro (dark mode)
- Filtros dinâmicos, busca e paginação
- Importação/exportação de equipamentos via CSV
- Totalmente responsivo e otimizado para mobile

## Requisitos
- PHP >= 8.1
- Composer
- Node.js >= 18
- NPM ou Yarn
- SQLite (padrão) ou MySQL

## Instalação
```bash
# Clone o repositório
git clone https://github.com/marcelov7/sistema-relatorios.git
cd sistema-relatorios

# Instale as dependências PHP
composer install

# Instale as dependências JS
npm install

# Copie o arquivo de ambiente e configure
cp .env.example .env
# Edite o .env conforme necessário (DB_CONNECTION, etc)

# Gere a chave da aplicação
php artisan key:generate

# Rode as migrations e seeders (irá popular com dados de exemplo)
php artisan migrate:fresh --seed

# Inicie o servidor Laravel
php artisan serve

# Em outro terminal, inicie o Vite (frontend)
npm run dev
```

## Usuário Administrador Padrão
- **Email:** admin@sistema.com
- **Senha:** admin123

Usuários de exemplo também são criados via seeder.

## Estrutura de Pastas
```
├── app/Http/Controllers      # Controllers Laravel
├── app/Models                # Models Eloquent
├── database/migrations       # Migrations
├── database/seeders          # Seeders
├── resources/js/Pages        # Páginas Vue (SPA)
├── resources/js/Components   # Componentes Vue reutilizáveis
├── resources/views           # Views Blade (PDF, etc)
├── public/                   # Assets públicos
├── routes/                   # Rotas Laravel
├── tests/                    # Testes automatizados
```

## Comandos Úteis
- `php artisan migrate:fresh --seed` — recria o banco e popula com dados de exemplo
- `php artisan db:seed --class=AdminUserSeeder` — cria admin e usuários de teste
- `npm run dev` — inicia o frontend com Vite
- `php artisan serve` — inicia o backend Laravel

## Contribuindo
1. Faça um fork do projeto
2. Crie uma branch: `git checkout -b minha-feature`
3. Commit suas alterações: `git commit -m 'feat: minha feature'`
4. Push para o fork: `git push origin minha-feature`
5. Abra um Pull Request

## Licença
MIT

---

> Desenvolvido por Marcelo V. e colaboradores. Dúvidas ou sugestões? Abra uma issue!