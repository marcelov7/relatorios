# ✅ Checklist de Atualização - Sistema de Motores

## 📋 Preparação (Antes da Atualização)

### ☐ Backup de Segurança
- [ ] **BACKUP DO BANCO DE DADOS** (OBRIGATÓRIO)
  ```bash
  mysqldump -u [usuario] -p [banco] > backup_motores_$(date +%Y%m%d_%H%M%S).sql
  ```
- [ ] Backup do arquivo `.env`
- [ ] Backup dos arquivos de upload (se houver)

### ☐ Verificação do Ambiente
- [ ] Confirmar que está em ambiente de produção
- [ ] Verificar se `APP_ENV=production` no .env
- [ ] Verificar se `APP_DEBUG=false` no .env

## 🚀 Processo de Atualização

### ☐ Atualização do Código
- [ ] Fazer pull das últimas alterações do Git
  ```bash
  git pull origin master
  ```
- [ ] Verificar se todos os arquivos foram atualizados

### ☐ Dependências
- [ ] Atualizar dependências PHP
  ```bash
  composer install --optimize-autoloader --no-dev
  ```
- [ ] Atualizar dependências Node.js
  ```bash
  npm ci
  npm run build
  ```

### ☐ Migrações do Sistema de Motores
- [ ] Verificar status das migrações
  ```bash
  php artisan migrate:status
  ```
- [ ] Executar migrações específicas do sistema de motores:
  - [ ] `2024_01_01_000000_create_motores_table`
  - [ ] `2024_01_01_000001_create_relatorio_motor_table`
  - [ ] `2025_07_17_204222_update_motores_table_structure`
  - [ ] `2025_07_17_205503_update_motores_local_reserva_fields`
- [ ] Executar migrações
  ```bash
  php artisan migrate --force
  ```

### ☐ Otimizações
- [ ] Limpar caches antigos
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```
- [ ] Recriar caches otimizados
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

### ☐ Estrutura de Diretórios
- [ ] Criar diretório para uploads de motores
  ```bash
  mkdir -p storage/app/public/motores
  ```

## 🔍 Verificações Pós-Atualização

### ☐ Arquivos Obrigatórios
- [ ] `app/Models/Motor.php` - Modelo do motor
- [ ] `app/Http/Controllers/MotorController.php` - Controller
- [ ] `resources/js/Pages/Motores/Index.vue` - Listagem
- [ ] `resources/js/Pages/Motores/Create.vue` - Criação
- [ ] `resources/js/Pages/Motores/Edit.vue` - Edição
- [ ] `resources/js/Pages/Motores/Show.vue` - Visualização
- [ ] `routes/web.php` - Rotas configuradas

### ☐ Funcionalidades do Sistema de Motores
- [ ] **Acesso à listagem**: `/motores`
- [ ] **Criar motor**: Formulário funciona
- [ ] **Editar motor**: Edição funciona
- [ ] **Visualizar motor**: Detalhes exibem corretamente
- [ ] **Excluir motor**: Exclusão funciona
- [ ] **Filtros**: Busca e filtros funcionam
- [ ] **Upload de foto**: Upload funciona
- [ ] **Responsividade**: Teste em mobile

### ☐ Integração com Sistema Existente
- [ ] **Menu de navegação**: Link para motores aparece
- [ ] **Dashboard**: Não quebrou funcionalidades existentes
- [ ] **Relatórios**: Sistema de relatórios continua funcionando
- [ ] **Usuários**: Login/logout funcionam
- [ ] **Permissões**: Controle de acesso funciona

### ☐ Performance
- [ ] Página de motores carrega em <3 segundos
- [ ] Filtros respondem rapidamente
- [ ] Upload de imagens funciona
- [ ] CSS/JS carregam corretamente

## 🧪 Testes Específicos

### ☐ Teste de Criação
- [ ] Preencher todos os campos obrigatórios
- [ ] Upload de foto
- [ ] Salvar motor
- [ ] Verificar se aparece na listagem

### ☐ Teste de Edição
- [ ] Editar um motor existente
- [ ] Alterar foto
- [ ] Salvar alterações
- [ ] Verificar se mudanças foram aplicadas

### ☐ Teste de Filtros
- [ ] Busca por texto
- [ ] Filtro por local
- [ ] Filtro por armazenamento
- [ ] Filtro por reserva
- [ ] Filtros avançados (potência, rotação, etc.)

### ☐ Teste Mobile
- [ ] Acessar em smartphone
- [ ] Testar formulários em mobile
- [ ] Verificar responsividade
- [ ] Testar upload de foto em mobile

## 🛡️ Segurança

### ☐ Verificações de Segurança
- [ ] Validações de formulário funcionam
- [ ] Upload de arquivos é seguro
- [ ] Permissões de acesso funcionam
- [ ] CSRF protection ativo

### ☐ Logs e Monitoramento
- [ ] Verificar logs de erro
  ```bash
  tail -f storage/logs/laravel.log
  ```
- [ ] Monitorar por erros nas próximas horas
- [ ] Verificar se não há queries lentas

## 📞 Em Caso de Problemas

### 🔧 Comandos de Diagnóstico
```bash
# Ver logs de erro
tail -f storage/logs/laravel.log

# Verificar status das migrações
php artisan migrate:status

# Limpar caches
php artisan cache:clear
php artisan config:clear

# Verificar rotas
php artisan route:list | grep motores

# Testar conexão com banco
php artisan tinker
>>> DB::connection()->getPdo();
```

### 🚨 Problemas Comuns
- **Erro 500**: Verificar logs e permissões
- **Página não encontrada**: Verificar rotas e cache
- **CSS não carrega**: Executar `npm run build`
- **Upload não funciona**: Verificar permissões do storage
- **Filtros não funcionam**: Verificar JavaScript e cache

## ✅ Atualização Concluída

### ☐ Finalização
- [ ] Todos os testes passaram
- [ ] Sistema de motores está funcionando
- [ ] Sistema existente não foi afetado
- [ ] Usuários podem acessar normalmente
- [ ] Logs estão limpos

### 📋 Informações para Entrega
```
Sistema: Sistema de Relatórios V1.00
Atualização: Sistema de Motores
URL: https://seu-dominio.com/motores
Funcionalidades: CRUD completo, filtros, upload, mobile
Status: ATIVO
```

---

**🎉 Parabéns! O Sistema de Motores foi atualizado com sucesso!**

### 📞 Suporte
Para suporte ou dúvidas:
- Verificar logs: `storage/logs/laravel.log`
- Documentação: `CHECKLIST-DEPLOY.md`
- Script de atualização: `update-motores.ps1` 