# Solução Completa - PDF com Browsershot 📄

## Problema Inicial
```
View [relatorios.pdf-browsershot] not found
```

## ✅ Soluções Implementadas

### 1. View Template Criada
- **Arquivo**: `resources/views/relatorios/pdf-browsershot.blade.php`
- **Status**: ✅ Implantado com sucesso
- **Tamanho**: 3421 bytes
- **Permissões**: devaxis-app:devaxis-app

### 2. Chromium Browser Instalado
```bash
# Comando executado
snap install chromium

# Versão instalada
Chromium 138.0.7204.157 snap

# Localização
/snap/bin/chromium -> /usr/bin/snap
```

### 3. RelatorioController Atualizado
- **Método**: `pdfBrowsershot()`
- **Caminho do Chrome**: Prioriza `/snap/bin/chromium`
- **Fallback**: `/usr/bin/chromium-browser`
- **Status**: ✅ Implantado

### 4. PATH Configurado
```bash
# Adicionado ao /etc/environment
export PATH=/snap/bin:$PATH
```

## 🔧 Código do Controller

```php
public function pdfBrowsershot($id)
{
    try {
        $relatorio = Relatorio::findOrFail($id);
        
        // Detectar caminho do Chrome/Chromium
        $chromePaths = [
            '/snap/bin/chromium',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/google-chrome'
        ];
        
        $chromePath = null;
        foreach ($chromePaths as $path) {
            if (file_exists($path)) {
                $chromePath = $path;
                break;
            }
        }
        
        if (!$chromePath) {
            throw new \Exception('Chrome/Chromium não encontrado no sistema');
        }
        
        Log::info("Gerando PDF com Browsershot usando Chrome: $chromePath");
        
        $html = view('relatorios.pdf-browsershot', compact('relatorio'))->render();
        
        $pdf = Browsershot::html($html)
            ->setChromePath($chromePath)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->timeout(60)
            ->pdf();
            
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="relatorio-' . $relatorio->id . '.pdf"');
            
    } catch (\Exception $e) {
        Log::error('Erro ao gerar PDF com Browsershot: ' . $e->getMessage());
        return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
    }
}
```

## 📝 Template PDF (pdf-browsershot.blade.php)

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório {{ $relatorio->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .content {
            line-height: 1.6;
        }
        .image-container {
            text-align: center;
            margin: 20px 0;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Manutenção</h1>
        <p><strong>ID:</strong> {{ $relatorio->id }}</p>
        <p><strong>Data:</strong> {{ $relatorio->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="content">
        <h2>Dados do Equipamento</h2>
        <p><strong>Equipamento:</strong> {{ $relatorio->equipamento->nome ?? 'N/A' }}</p>
        <p><strong>Setor:</strong> {{ $relatorio->setor->nome ?? 'N/A' }}</p>
        
        <h2>Descrição do Problema</h2>
        <div>{!! nl2br(e($relatorio->descricao_problema)) !!}</div>
        
        <h2>Solução Aplicada</h2>
        <div>{!! nl2br(e($relatorio->solucao_aplicada)) !!}</div>
        
        @if($relatorio->observacoes)
        <h2>Observações</h2>
        <div>{!! nl2br(e($relatorio->observacoes)) !!}</div>
        @endif
        
        @if($relatorio->imagens && count($relatorio->imagens) > 0)
        <h2>Imagens</h2>
        @foreach($relatorio->imagens as $imagem)
        <div class="image-container">
            <img src="{{ public_path('storage/' . $imagem->caminho) }}" alt="Imagem {{ $loop->iteration }}">
            @if($imagem->descricao)
            <p><em>{{ $imagem->descricao }}</em></p>
            @endif
        </div>
        @endforeach
        @endif
    </div>

    <div class="footer">
        <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
```

## 🧪 Como Testar

### 1. Teste Direto via URL
```
https://app.devaxis.com.br/relatorios/{id}/pdf-browsershot
```

### 2. Verificar Logs
```bash
ssh root@31.97.168.137
tail -f /home/devaxis-app/htdocs/app.devaxis.com.br/storage/logs/laravel.log
```

### 3. Teste do Chromium
```bash
ssh root@31.97.168.137
/snap/bin/chromium --version
```

### 4. Testar com Argumentos de Produção
```bash
# Verificar diretório temporário
ssh root@31.97.168.137
ls -la /tmp/chromium-data/
```

## 🔧 Correções de Produção Implementadas

### **Erro Original:**
```
ProcessFailedException: cannot create snap home dir: mkdir /var/www/snap: permission denied
```

### **Soluções Aplicadas:**

#### 1. **Argumentos Robustos do Chromium**
```php
->addChromiumArguments([
    '--no-sandbox',
    '--disable-gpu',
    '--disable-dev-shm-usage',
    '--disable-setuid-sandbox',
    '--user-data-dir=/tmp/chromium-data',
    '--data-path=/tmp/chromium-data',
    '--disable-background-timer-throttling',
    '--disable-backgrounding-occluded-windows',
    '--disable-renderer-backgrounding',
    '--disable-features=TranslateUI',
    '--disable-ipc-flooding-protection'
])
```

#### 2. **Diretório Temporário Criado**
```bash
mkdir -p /tmp/chromium-data
chmod 777 /tmp/chromium-data
```

#### 3. **Timeout Aumentado**
```php
->timeout(120)  // 2 minutos para PDF complexos
```

#### 4. **Rota Individual Configurada**
```php
// routes/web.php
Route::get('/relatorios/{id}/pdf-browsershot', [RelatorioController::class, 'pdfBrowsershot'])->name('relatorios.pdf-browsershot');
```

## 📊 Status Final

| Componente | Status | Detalhes |
|------------|--------|----------|
| View Template | ✅ OK | pdf-browsershot.blade.php criado |
| Chromium Browser | ✅ OK | v138.0.7204.157 instalado via snap |
| Controller | ✅ OK | Método pdfBrowsershot() implementado |
| PATH System | ✅ OK | /snap/bin adicionado ao PATH |
| Permissões | ✅ OK | devaxis-app:devaxis-app configurado |

## 🎯 Próximos Passos

1. **Testar geração de PDF** - Acessar URL com ID de relatório válido
2. **Verificar imagens** - Confirmar se imagens aparecem no PDF
3. **Performance** - Monitorar tempo de geração
4. **Logs** - Verificar se não há erros no log

## 🔒 Segurança Mantida

- ✅ Sistema de imagens intocado (conforme solicitado)
- ✅ Página de relatórios não modificada
- ✅ Apenas adicionada funcionalidade de PDF
- ✅ Logs implementados para debug

---
**Implementado em**: 26/07/2024 01:27  
**Servidor**: CloudPanel SSH root@31.97.168.137  
**Status**: Pronto para teste ✅
