# 🚀 **Instruções Snappy - CloudPanel**

## 📋 **Passo a Passo**

### **1. Copiar Arquivos**
Copie estes arquivos para o CloudPanel:
- `app/Console/Commands/TestarSnappy.php`
- `app/Console/Commands/LimparCacheComandos.php`
- `app/Http/Controllers/RelatorioController.php` (atualizado)

### **2. Limpar Cache**
```bash
php artisan limpar:comandos
```

### **3. Testar Snappy**
```bash
php artisan testar:snappy
```

### **4. Verificar Comandos**
```bash
php artisan list | grep -i snappy
```

## 🔧 **Comandos Disponíveis**

### **Testar Snappy**
```bash
php artisan testar:snappy
```
- Verifica se Snappy está instalado
- Procura wkhtmltopdf
- Gera PDF de teste
- Mostra resumo

### **Limpar Cache**
```bash
php artisan limpar:comandos
```
- Limpa todos os caches
- Recarrega configurações
- Lista comandos disponíveis

## 🎯 **Teste Rápido**

### **1. Verificar Snappy**
```bash
php artisan testar:snappy
```

### **2. Se funcionar, testar URLs**
- `https://app.devaxis.com.br/teste-rota`
- `https://app.devaxis.com.br/gerar-pdf?ids=85,84&template=padrao`

### **3. Verificar PDFs gerados**
- `storage/app/public/teste-snappy-simples.pdf`

## 📊 **Resultados Esperados**

### **✅ Sucesso**
```
🧪 Testando Snappy...
✅ Snappy está instalado
✅ wkhtmltopdf encontrado em: /usr/bin/wkhtmltopdf
📋 Versão: wkhtmltopdf 0.12.6
🔄 Gerando PDF de teste...
✅ PDF gerado com sucesso!
📁 Arquivo: /path/to/teste-snappy-simples.pdf
📏 Tamanho: 45.23 KB
🎉 Snappy funcionando no CloudPanel!
```

### **⚠️ Problemas**
- wkhtmltopdf não encontrado
- Erro de permissão
- Erro de dependências

## 🔍 **Solução de Problemas**

### **wkhtmltopdf não encontrado**
```bash
# Tentar instalar
apt-get update && apt-get install -y wkhtmltopdf

# Ou baixar manualmente
wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.bionic_amd64.deb
dpkg -i wkhtmltox_0.12.6-1.bionic_amd64.deb
```

### **Erro de permissão**
```bash
chmod +x /usr/bin/wkhtmltopdf
chown www-data:www-data /usr/bin/wkhtmltopdf
```

### **Comandos não aparecem**
```bash
php artisan limpar:comandos
composer dump-autoload
```

## 📞 **Suporte**

Se houver problemas:
1. Execute `php artisan testar:snappy`
2. Copie a saída completa
3. Verifique logs em `storage/logs/laravel.log`
4. Teste URLs alternativas

---

**🎉 Snappy oferece melhor qualidade de PDFs!** 