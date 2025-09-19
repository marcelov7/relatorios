<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório {{ isset($relatorio) ? $relatorio->id : 'PDF' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1976d2;
        }
        
        .header h1 {
            color: #1976d2;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .meta-info {
            color: #666;
            font-size: 11px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .relatorio-card {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
            page-break-inside: avoid;
        }
        
        .relatorio-title {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-item {
            font-size: 12px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .detalhes-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .detalhes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        .detalhes-content {
            font-size: 12px;
            line-height: 1.5;
            color: #444;
            white-space: pre-wrap;
        }
        
        .equipamentos-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .equipamento-item {
            background: #f5f5f5;
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .images-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .image-item {
            text-align: center;
        }
        
        .image-item img {
            max-width: 100%;
            max-height: 120px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .image-caption {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            color: #999;
            font-size: 10px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-aberta { background: #fff3cd; color: #856404; }
        .status-andamento { background: #d1ecf1; color: #0c5460; }
        .status-concluida { background: #d4edda; color: #155724; }
        .status-cancelada { background: #f8d7da; color: #721c24; }
        
        @media print {
            body { padding: 15px; }
            .relatorio-card { box-shadow: none; }
            .header, .footer { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ isset($relatorios) ? 'Relatórios de Manutenção' : 'Relatório de Manutenção' }}</h1>
        </div>
        
        <div class="meta-info">
            Gerado em: {{ isset($data_geracao) ? $data_geracao : now()->format('d/m/Y H:i') }}<br>
            Sistema: {{ isset($nome_sistema) ? $nome_sistema : config('app.name', 'Sistema de Relatórios') }}
        </div>
        
        @if(isset($relatorios))
            {{-- Múltiplos relatórios --}}
            @foreach($relatorios as $rel)
                @include('relatorios.pdf-single-report', ['relatorio' => $rel])
            @endforeach
        @elseif(isset($relatorio))
            {{-- Relatório único --}}
            @include('relatorios.pdf-single-report', ['relatorio' => $relatorio])
        @else
            <div class="relatorio-card">
                <p>Nenhum relatório encontrado.</p>
            </div>
        @endif
        
        <div class="footer">
            <p>Relatório gerado automaticamente pelo sistema em {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 