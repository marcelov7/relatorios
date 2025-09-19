# 🔧 Correção de Problemas de PDF no CloudPanel

## ❌ **Problema Identificado**
Erro 404 ao tentar gerar PDFs com múltiplos relatórios no CloudPanel:
```
GET https://app.devaxis.com.br/relatorios/pdf-lote?ids=85&template=padrao
Status: 404 | NOT FOUND
```

## ✅ **Soluções Implementadas**

### 1. **Rotas Alternativas**
Adicionadas rotas simplificadas para evitar problemas de roteamento:

```php
// Rotas principais
Route::get('/relatorios/pdf-lote', [RelatorioController::class, 'pdfLote']);
Route::get('/relatorios/pdf-intercement', [RelatorioController::class, 'pdfInterCement']);

// Rotas alternativas para CloudPanel
Route::get('/pdf-lote', [RelatorioController::class, 'pdfLote']);
Route::get('/pdf-intercement', [RelatorioController::class, 'pdfInterCement']);

// Rotas diretas para CloudPanel (sem namespace)
Route::get('/gerar-pdf', [RelatorioController::class, 'pdfLote']);
Route::get('/gerar-pdf-intercement', [RelatorioController::class, 'pdfInterCement']);

// Rotas de teste para debug
Route::get('/teste-rota', [TesteController::class, 'testeRota']);
Route::get('/teste-pdf', [TesteController::class, 'testePdf']);
```

### 2. **JavaScript com Fallback**
Frontend tenta rota principal, depois alternativa:

```javascript
const gerarPdf = (template = 'padrao') => {
    const ids = selectedIds.value.join(',')
    const url = `/relatorios/pdf-lote?ids=${ids}&template=${template}`
    const urlAlternativa = `/pdf-lote?ids=${ids}&template=${template}`
    
    const newWindow = window.open(url, '_blank')
    if (!newWindow || newWindow.closed) {
        window.open(urlAlternativa, '_blank')
    }
}
```

### 3. **Controller Otimizado**
Aceita tanto GET quanto POST, com conversão automática de parâmetros:

```php
public function pdfLote(Request $request)
{
    $ids = $request->input('ids', []);
    
    // Se ids vier como string (GET), converter para array
    if (is_string($ids)) {
        $ids = array_filter(explode(',', $ids));
    }
    
    // Validações e geração do PDF...
}
```

### 4. **.htaccess Otimizado**
Configurações específicas para CloudPanel:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
    
    # Security Headers
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options nosniff
        Header always set X-Frame-Options DENY
    </IfModule>
</IfModule>
```

## 🧪 **Comandos de Teste**

### Limpar Cache (IMPORTANTE)
```bash
php artisan limpar:cache-cloudpanel
```

### Testar Rotas
```bash
php artisan testar:rotas-cloudpanel
```

### Testar PDF com Múltiplos Relatórios
```bash
php artisan testar:pdf-multiplos 3
```

### Testar Template Padrão
```bash
php artisan testar:pdf-padrao
```

## 🔗 **URLs de Teste**

### Rotas Principais
- `/relatorios/pdf-lote?ids=1,2,3&template=padrao`
- `/relatorios/pdf-intercement?ids=1,2,3`

### Rotas Alternativas (CloudPanel)
- `/pdf-lote?ids=1,2,3&template=padrao`
- `/pdf-intercement?ids=1,2,3`

### Rotas Diretas (CloudPanel)
- `/gerar-pdf?ids=1,2,3&template=padrao`
- `/gerar-pdf-intercement?ids=1,2,3`

### Rotas de Teste
- `/teste-rota` - Teste básico de rota
- `/teste-pdf?ids=1,2,3` - Teste de recebimento de dados

## 🛠️ **Configurações do CloudPanel**

### 1. **Verificar mod_rewrite**
```bash
# No CloudPanel, verificar se está habilitado
apache2ctl -M | grep rewrite
```

### 2. **Verificar .htaccess**
```bash
# Confirmar que o arquivo está sendo lido
tail -f /var/log/apache2/error.log
```

### 3. **Permissões de Arquivo**
```bash
# Garantir permissões corretas
chmod 644 public/.htaccess
chmod 755 public/
```

## 📋 **Checklist de Verificação**

- [ ] Rotas alternativas implementadas
- [ ] JavaScript com fallback funcionando
- [ ] Controller aceitando GET e POST
- [ ] .htaccess otimizado
- [ ] mod_rewrite habilitado
- [ ] Permissões de arquivo corretas
- [ ] Logs do servidor verificados

## 🚀 **Deploy no CloudPanel**

### **Passo 1: Instalar Snappy (Recomendado)**
```bash
composer require barryvdh/laravel-snappy
```

### **Passo 2: Configurar Snappy**
```bash
php artisan configurar:snappy-cloudpanel
```

### **Passo 3: Limpar Cache**
```bash
php artisan limpar:cache-cloudpanel
```

### **Passo 4: Testar Snappy**
```bash
php artisan testar:snappy-cloudpanel
```

### **Passo 5: Testar URLs**
1. `https://app.devaxis.com.br/teste-rota`
2. `https://app.devaxis.com.br/teste-pdf?ids=85,84`
3. `https://app.devaxis.com.br/gerar-pdf?ids=85,84&template=padrao`

### **Passo 6: Verificar Funcionamento**
- Snappy oferece melhor qualidade de imagens
- DomPDF como fallback automático
- Logs detalhados para debug

## 📞 **Suporte**

Se o problema persistir:
1. Verificar logs do CloudPanel
2. Testar rotas alternativas
3. Confirmar configurações do servidor
4. Verificar se o Laravel está funcionando corretamente 