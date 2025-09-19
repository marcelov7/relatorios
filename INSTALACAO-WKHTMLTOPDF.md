# Instalação do wkhtmltopdf para Snappy

## 🎯 Objetivo
Instalar o wkhtmltopdf para usar o Snappy (melhor qualidade de PDF com imagens) no sistema de relatórios.

## 📥 Download

### Windows
1. Acesse: https://wkhtmltopdf.org/downloads.html
2. Baixe a versão **Windows x64** (64-bit)
3. Escolha a versão estável mais recente

### Alternativa (Chocolatey)
```powershell
choco install wkhtmltopdf
```

## 🔧 Instalação

### Instalação Manual
1. Execute o arquivo `.exe` baixado
2. Siga o assistente de instalação
3. **Importante**: Anote o caminho de instalação (geralmente `C:\Program Files\wkhtmltopdf\bin\`)

### Verificação da Instalação
Abra o PowerShell e execute:
```powershell
wkhtmltopdf --version
```

Se funcionar, você verá algo como:
```
wkhtmltopdf 0.12.6 (with patched qt)
```

## ⚙️ Configuração no Laravel

### 1. Adicionar ao arquivo .env
```env
WKHTML_PDF_BINARY="\"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe\""
```

### 2. Verificar configuração
```bash
php artisan config:cache
```

### 3. Testar
```bash
php artisan testar:pdf-snappy
```

## 🎨 Vantagens do Snappy

### Comparação: DomPDF vs Snappy

| Característica | DomPDF | Snappy |
|----------------|--------|--------|
| **Qualidade de Imagens** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Suporte CSS3** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Fontes** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Performance** | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Instalação** | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Compatibilidade** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |

### Benefícios Específicos
- ✅ **Imagens em alta resolução** (300 DPI)
- ✅ **CSS3 completo** (gradientes, sombras, etc.)
- ✅ **Fontes web** (Google Fonts, etc.)
- ✅ **Layout responsivo** perfeito
- ✅ **Qualidade de impressão** profissional

## 🚀 Uso no Sistema

### Interface
- Botão **"PDF Snappy"** (laranja) na listagem de relatórios
- Gera PDF com qualidade superior
- Ideal para relatórios com imagens

### Comandos Disponíveis
```bash
# Testar Snappy
php artisan testar:pdf-snappy

# Testar DomPDF (comparação)
php artisan testar:pdf-intercement
```

## 🔧 Solução de Problemas

### Erro: "wkhtmltopdf não encontrado"
1. Verifique se o caminho no `.env` está correto
2. Teste o comando: `wkhtmltopdf --version`
3. Adicione o caminho ao PATH do sistema

### Erro: "Permission denied"
1. Execute como administrador
2. Verifique permissões da pasta de instalação

### Erro: "Binary not found"
1. Reinstale o wkhtmltopdf
2. Use caminho absoluto no `.env`
3. Reinicie o servidor web

## 📋 Checklist de Instalação

- [ ] Download do wkhtmltopdf
- [ ] Instalação concluída
- [ ] Teste `wkhtmltopdf --version`
- [ ] Configuração no `.env`
- [ ] Teste `php artisan testar:pdf-snappy`
- [ ] Verificação na interface web

## 🎯 Próximos Passos

Após a instalação:
1. Teste a geração de PDF com Snappy
2. Compare a qualidade com DomPDF
3. Use Snappy para relatórios com imagens
4. Mantenha DomPDF como fallback

---

**Nota**: O Snappy oferece qualidade superior, mas requer instalação adicional. Para produção, considere instalar em todos os servidores. 