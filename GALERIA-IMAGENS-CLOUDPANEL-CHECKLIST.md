# 🖼️ Checklist - Galeria de Imagens no CloudPanel

## 🚨 **Problemas Potenciais e Soluções**

### **1. 📁 Permissões de Arquivos**

#### **Problema:**
- Imagens não são salvas corretamente
- Erro 500 ao fazer upload
- Thumbnails não são gerados

#### **Solução:**
```bash
# Configurar permissões corretas
chmod -R 775 storage/app/public
chmod -R 775 public/storage
chown -R www-data:www-data storage/
chown -R www-data:www-data public/storage/
```

---

### **2. 🔗 Link Simbólico (storage:link)**

#### **Problema:**
- Imagens são salvas mas não aparecem no frontend
- Erro 404 ao acessar imagens
- Path `/storage/relatorios/imagem.jpg` não funciona

#### **Solução:**
```bash
# Criar link simbólico
php artisan storage:link

# Verificar se o link foi criado
ls -la public/storage

# Se não funcionar, criar manualmente
ln -sf /caminho/completo/storage/app/public /caminho/completo/public/storage
```

---

### **3. 📏 Limites de Upload do PHP**

#### **Problema:**
- Upload falha para imagens grandes
- Timeout no upload
- Erro "File too large"

#### **Verificar no CloudPanel:**
```ini
# php.ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 3000
```

#### **Verificar no Laravel:**
```php
// config/filesystems.php - já configurado
// Limite de 10MB por imagem no código
'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
```

---

### **4. 🔄 Sincronização Automática CloudPanel**

#### **Problema:**
- Função `syncImageToPublic()` pode falhar
- Imagens ficam apenas em `storage/app/public` 
- Não aparecem em `public/storage`

#### **Verificação:**
```bash
# Verificar se a função está executando
tail -f storage/logs/laravel.log | grep sync

# Testar manualmente
php -r "
include 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$controller = new App\Http\Controllers\RelatorioController();
// Teste da função sync
"
```

#### **Solução Manual (se necessário):**
```bash
# Script para sincronizar imagens
php sync_relatorio_images.php
```

---

### **5. 🏗️ Estrutura de Diretórios**

#### **Verificar se existem:**
```bash
# Estrutura necessária
storage/app/public/relatorios/
storage/app/public/relatorios/thumbs/
storage/app/public/atualizacoes/
public/storage/relatorios/
public/storage/relatorios/thumbs/
public/storage/atualizacoes/
```

#### **Criar se não existir:**
```bash
mkdir -p storage/app/public/relatorios/thumbs
mkdir -p storage/app/public/atualizacoes
mkdir -p public/storage/relatorios/thumbs
mkdir -p public/storage/atualizacoes
```

---

### **6. 🖼️ Processamento de Thumbnails**

#### **Problema:**
- GD Extension não instalada
- Thumbnails não são gerados
- Erro ao redimensionar imagens

#### **Verificar:**
```bash
# Verificar se GD está instalada
php -m | grep -i gd

# Ou verificar Imagick
php -m | grep -i imagick
```

#### **Configuração no código:**
```php
// RelatorioController.php - linha ~210
$manager = new ImageManager(new GdDriver());
$img = $manager->read($image)->cover(600, 400);
```

---

### **7. 🌐 URLs e Paths Corretos**

#### **Problema:**
- URLs das imagens quebradas em produção
- Path incorreto no frontend

#### **Verificar .env:**
```env
APP_URL=https://seu-dominio.com
FILESYSTEM_DISK=public
```

#### **Verificar no código:**
```javascript
// Frontend - resources/js/Pages/Relatorios/
// URLs devem ser: /storage/relatorios/imagem.jpg
const imageUrl = `/storage/${image.path}`
```

---

### **8. 📦 Configurações do CloudPanel**

#### **Document Root:**
- Deve apontar para `/public`
- NÃO para a raiz do projeto

#### **PHP-FPM:**
```ini
# Verificar limites do PHP-FPM
pm.max_children = 50
pm.max_requests = 500
request_terminate_timeout = 300s
```

#### **Nginx/Apache:**
```nginx
# Nginx - permitir uploads grandes
client_max_body_size 20M;

# Apache - .htaccess já configurado
php_value upload_max_filesize 20M
php_value post_max_size 20M
```

---

### **9. 🔒 Segurança de Uploads**

#### **Validações implementadas:**
- Tipos de arquivo permitidos: `jpeg,png,jpg,gif`
- Tamanho máximo: 10MB por imagem
- Sanitização de nomes de arquivos
- Geração de nomes únicos

#### **Verificar:**
```php
// RelatorioController.php - linha ~200+
$fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
```

---

### **10. 🔍 Monitoramento e Debug**

#### **Logs importantes:**
```bash
# Laravel
tail -f storage/logs/laravel.log

# Servidor web (CloudPanel)
tail -f /var/log/nginx/error.log
# ou
tail -f /var/log/apache2/error.log

# PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

#### **Comandos de teste:**
```bash
# Testar upload via curl
curl -X POST https://seu-dominio.com/relatorios \
  -F "titulo=Teste" \
  -F "images[]=@teste.jpg" \
  -H "Authorization: Bearer TOKEN"

# Verificar espaço em disco
df -h

# Verificar inodes
df -i
```

---

## ✅ **Checklist de Deploy**

### **Antes do Deploy:**
- [ ] Backup das imagens existentes
- [ ] Verificar espaço em disco disponível
- [ ] Testar uploads em ambiente de teste

### **Durante o Deploy:**
- [ ] Configurar permissões corretas
- [ ] Criar link simbólico (`php artisan storage:link`)
- [ ] Verificar configurações PHP
- [ ] Testar upload de uma imagem

### **Após o Deploy:**
- [ ] Verificar se imagens antigas ainda aparecem
- [ ] Testar upload de nova imagem
- [ ] Verificar geração de thumbnails
- [ ] Testar visualização no modal de imagens
- [ ] Verificar logs de erro

---

### **11. � Problema de Substituição de Imagens na Edição**

#### **Problema:**
- Ao editar um relatório e tentar substituir uma imagem, ela não é substituída
- A imagem antiga permanece mesmo após upload da nova
- Erro "Permission denied" ao tentar deletar arquivos
- Falha no `unlink()` de arquivos em `public/storage/`

#### **Diagnóstico:**
O problema ocorre porque:
1. O PHP-FPM roda com usuário diferente do proprietário dos arquivos (`devaxis-app`)
2. A função `unlink()` nativa não tem permissão para deletar arquivos
3. O processo de substituição falha na exclusão, impedindo a criação de novos arquivos

#### **Solução Implementada:**
```php
// ImageUploadService.php - Método deletePublicFile() melhorado
private function deletePublicFile(string $filePath): bool
{
    // Método 1: unlink direto
    try {
        if (unlink($filePath)) {
            return true;
        }
    } catch (Exception $e) {
        // Continua para próximo método
    }

    // Método 2: exec com rm (mais confiável)
    try {
        $escapedPath = escapeshellarg($filePath);
        exec("rm -f {$escapedPath} 2>&1", $output, $returnCode);
        
        if ($returnCode === 0 && !file_exists($filePath)) {
            return true;
        }
    } catch (Exception $e) {
        // Continua para próximo método
    }

    // Método 3: renomear para .deleted (backup)
    try {
        $deletedPath = $filePath . '.deleted_' . time();
        if (rename($filePath, $deletedPath)) {
            @unlink($deletedPath);
            return true;
        }
    } catch (Exception $e) {
        // Log do erro
    }

    return false;
}
```

#### **Verificação da Correção:**
```bash
# 1. Testar edição de relatório
# 2. Substituir uma imagem existente
# 3. Verificar se a nova imagem aparece
# 4. Confirmar que a antiga foi removida

# Verificar logs
tail -f storage/logs/laravel.log | grep -E "DELETE|UPLOAD"
```

#### **Funcionalidades Adicionais:**
- **Múltiplos métodos de deleção**: fallback automático se um método falhar
- **Logging detalhado**: rastreamento completo do processo de deleção
- **Limpeza de arquivos .deleted**: comando para remover arquivos órfãos
- **Retorno de status**: indica se a deleção foi parcial ou completa

#### **Comando de Limpeza (Opcional):**
```php
// Adicionar ao RelatorioController ou criar comando Artisan
$imageService = new ImageUploadService();
$cleaned = $imageService->cleanupDeletedFiles();
echo "Arquivos limpos: {$cleaned}";
```

---

### **1. Perda de Imagens Existentes**
```bash
# SEMPRE fazer backup antes
tar -czf backup_images_$(date +%Y%m%d).tar.gz storage/app/public/relatorios/
```

### **2. Quebra do Sistema de Uploads**
- Testar em ambiente de desenvolvimento primeiro
- Manter função `syncImageToPublic()` ativa
- Verificar permissões antes de ir para produção

### **3. Performance Issues**
- Otimizar thumbnails (600x400px, 75% quality)
- Limitar quantidade de imagens por relatório (10 max)
- Implementar lazy loading se necessário

---

### **12. 📂 Problema de Criação de Diretórios**

#### **Problema:**
- Erro "Unable to create a directory" ao fazer upload de imagens
- Pastas de relatórios não são criadas automaticamente
- Falha no upload mesmo com permissões aparentemente corretas

#### **Diagnóstico:**
- O Laravel Storage pode falhar na criação de diretórios em ambientes CloudPanel
- PHP-FPM pode não ter permissões adequadas para criar estruturas de diretórios
- Método `putFileAs()` depende de diretórios já existentes

#### **Solução Implementada:**
```php
// ImageUploadService.php - Método ensureDirectoryExists()
private function ensureDirectoryExists(string $path): bool
{
    try {
        if (!$this->disk->exists($path)) {
            $fullPath = storage_path("app/public/{$path}");
            
            Log::info('DIRECTORY: Criando diretório', ['path' => $fullPath]);
            
            // Criar usando mkdir nativo para garantir criação
            if (!is_dir($fullPath)) {
                $created = mkdir($fullPath, 0775, true);
                if ($created) {
                    Log::info('DIRECTORY: Diretório criado com sucesso', ['path' => $fullPath]);
                    chmod($fullPath, 0775);
                    return true;
                } else {
                    Log::error('DIRECTORY: Falha ao criar diretório', ['path' => $fullPath]);
                    return false;
                }
            }
            
            return true;
        }
        
        return true;
    } catch (Exception $e) {
        Log::error('DIRECTORY: Erro ao criar diretório', [
            'path' => $path,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}
```

#### **Uso no Upload:**
```php
// Criar diretórios se não existirem ANTES do upload
$this->ensureDirectoryExists(dirname($originalPath));
$this->ensureDirectoryExists(dirname($thumbPath));
$this->ensureDirectoryExists(dirname($mediumPath));

// Depois fazer o upload normalmente
$originalSaved = $this->disk->putFileAs(...);
```

#### **Verificação:**
```bash
# Testar criação de novo relatório com imagens
# Verificar logs para confirmação de criação de diretórios
tail -f storage/logs/laravel.log | grep "DIRECTORY"

# Verificar estrutura criada
ls -la storage/app/public/relatorios/NUMERO_RELATORIO/
```

---

## 📞 **Comandos de Emergência**

### **Se imagens não aparecem:**
```bash
# 1. Recriar link simbólico
rm -f public/storage
php artisan storage:link

# 2. Sincronizar imagens manualmente
php sync_relatorio_images.php

# 3. Verificar permissões
chmod -R 775 storage/app/public public/storage
```

### **Se uploads falham:**
```bash
# 1. Verificar espaço
df -h

# 2. Verificar permissões
ls -la storage/app/public

# 3. Verificar logs
tail -f storage/logs/laravel.log
```

### **Se performance é lenta:**
```bash
# 1. Otimizar autoloader
composer dump-autoload --optimize

# 2. Cache de configuração
php artisan config:cache

# 3. Verificar tamanho das imagens
du -h storage/app/public/relatorios/
```

---

## 📋 **Teste Final**

Para validar que tudo está funcionando:

1. **Criar novo relatório** com 2-3 imagens
2. **Editar relatório** removendo uma imagem e adicionando outra
3. **Atualizar progresso** com novas imagens
4. **Verificar se todas as imagens aparecem** na visualização
5. **Testar modal de galeria** de imagens
6. **Verificar thumbnails** estão sendo gerados

Se todos os testes passarem, a galeria está funcionando corretamente no CloudPanel! 🎉
