# ✅ Checklist de Deploy - CloudPanel

## 📋 Preparação (Antes do Deploy)

### ☐ CloudPanel - Configuração do Banco
- [ ] Criar banco de dados: `relatodb`
- [ ] Criar usuário: `appuser`
- [ ] Definir senha: `M@rcelo1809@3033`
- [ ] Conceder privilégios completos ao usuário no banco

### ☐ CloudPanel - Configuração do Site
- [ ] Criar novo site/domínio
- [ ] Configurar Document Root para `/public`
- [ ] Configurar PHP 8.2+
- [ ] Verificar extensões PHP necessárias

### ☐ Arquivos do Sistema
- [ ] Upload/clone dos arquivos para o servidor
- [ ] Verificar se todos os arquivos foram transferidos
- [ ] Verificar permissões iniciais

## 🚀 Processo de Deploy

### ☐ Configuração do Ambiente
- [ ] Copiar `env-production.txt` para `.env`
- [ ] Editar `.env` com URL do domínio correto
- [ ] Verificar configurações do banco de dados
- [ ] Configurar email (se necessário)

### ☐ Dependências e Build
- [ ] Executar: `composer install --optimize-autoloader --no-dev`
- [ ] Executar: `npm ci && npm run build`
- [ ] Verificar se `node_modules` e `vendor` existem

### ☐ Laravel - Configuração
- [ ] Executar: `php artisan key:generate --force`
- [ ] Executar: `php artisan storage:link`
- [ ] Executar: `php artisan migrate --force`
- [ ] Executar: `php artisan db:seed --force`

### ☐ Otimizações de Produção
- [ ] Cache de configuração: `php artisan config:cache`
- [ ] Cache de rotas: `php artisan route:cache`
- [ ] Cache de views: `php artisan view:cache`

### ☐ Permissões
- [ ] `chmod -R 755 storage bootstrap/cache`
- [ ] `chmod -R 775 storage/app/public`
- [ ] Verificar se Apache/Nginx pode escrever nos diretórios

## 🔍 Verificações Pós-Deploy

### ☐ Conectividade
- [ ] Acessar homepage: `https://seu-dominio.com`
- [ ] Verificar se carrega corretamente
- [ ] Testar redirecionamento para HTTPS

### ☐ Funcionalidades Básicas
- [ ] Fazer login no sistema
- [ ] Navegar pelo dashboard
- [ ] Criar um relatório de teste
- [ ] Upload de imagem funciona
- [ ] Filtros funcionam corretamente

### ☐ Performance
- [ ] Verificar tempo de carregamento (<3s)
- [ ] Testar em mobile/tablet
- [ ] Verificar se CSS/JS carregam
- [ ] Testar dark theme

### ☐ Banco de Dados
- [ ] Executar: `php artisan migrate:status`
- [ ] Verificar se todas as tabelas existem
- [ ] Confirmar dados dos seeders

## 🛡️ Segurança

### ☐ Configurações de Segurança
- [ ] HTTPS configurado e funcionando
- [ ] `APP_DEBUG=false` no .env
- [ ] `APP_ENV=production` no .env
- [ ] Firewall configurado (se aplicável)

### ☐ Acesso Inicial
- [ ] Login como admin: `admin@sistema.com` / `admin123`
- [ ] **IMPORTANTE**: Alterar senha do admin
- [ ] Criar usuários adicionais se necessário

## 📂 CloudPanel - Configurações Finais

### ☐ PHP Settings
- [ ] `memory_limit = 256M`
- [ ] `upload_max_filesize = 20M`
- [ ] `post_max_size = 20M`
- [ ] `max_execution_time = 300`

### ☐ SSL/HTTPS
- [ ] Certificado SSL instalado
- [ ] Redirecionamento HTTP → HTTPS
- [ ] Verificar certificado válido

### ☐ Backup (Recomendado)
- [ ] Configurar backup automático do banco
- [ ] Configurar backup dos arquivos
- [ ] Testar processo de restore

## 🧪 Testes Finais

### ☐ Teste Completo do Sistema
- [ ] **Login/Logout**: Funciona corretamente
- [ ] **Dashboard**: Estatísticas aparecem corretas
- [ ] **Relatórios**: CRUD completo funciona
- [ ] **Upload**: Imagens são enviadas e exibidas
- [ ] **Filtros**: Todos os filtros funcionam
- [ ] **Responsividade**: Testa em mobile/tablet/desktop
- [ ] **Permissões**: Usuários normais vs admin
- [ ] **Dark Theme**: Alternância funciona

### ☐ Teste de Stress (Opcional)
- [ ] Criar múltiplos relatórios
- [ ] Upload de várias imagens
- [ ] Teste com vários usuários simultâneos

## 📞 Em Caso de Problemas

### 🔧 Comandos de Diagnóstico
```bash
# Ver logs de erro
tail -f storage/logs/laravel.log

# Limpar caches
php artisan cache:clear
php artisan config:clear

# Verificar permissões
ls -la storage/

# Testar banco
php artisan migrate:status
```

### 🚨 Problemas Comuns
- **Erro 500**: Verificar logs e permissões
- **CSS não carrega**: Executar `npm run build`
- **Imagens não aparecem**: Verificar `storage:link`
- **Banco não conecta**: Verificar credenciais no .env

## ✅ Deploy Concluído

### ☐ Finalização
- [ ] Todos os testes passaram
- [ ] Sistema está online e funcional
- [ ] Usuários podem acessar normalmente
- [ ] Backup configurado
- [ ] Documentação entregue

### 📋 Informações para Entrega
```
Sistema: Sistema de Relatórios V1.00
URL: https://seu-dominio.com
Admin: admin@sistema.com / [nova-senha]
Banco: relatodb
Versão: V1.00
```

---

**🎉 Parabéns! O Sistema de Relatórios V1.00 está pronto para produção!**

Para suporte ou dúvidas, consulte:
- `DEPLOY-CLOUDPANEL-V1.00.md` - Documentação completa
- `CHANGELOG-V1.00.md` - Lista de funcionalidades
- `storage/logs/laravel.log` - Logs do sistema 