<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios PDF</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header {
            padding: 24px 0 12px 0;
            border-bottom: 2px solid #1976d2;
            text-align: center;
        }
        .header h1 {
            color: #1976d2;
            font-size: 24px;
            margin: 0;
        }
        .meta {
            text-align: center;
            color: #888;
            font-size: 11px;
            margin-bottom: 16px;
        }
        .relatorio {
            margin: 0 0 32px 0;
            padding: 18px 24px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: #fafbfc;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            page-break-inside: avoid;
        }
        .relatorio .titulo {
            font-size: 16px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 4px;
        }
        .relatorio .info {
            font-size: 12px;
            color: #444;
            margin-bottom: 8px;
        }
        .relatorio .detalhes {
            margin-top: 8px;
            font-size: 12px;
            color: #222;
            white-space: pre-line;
        }
        .footer {
            text-align: center;
            color: #aaa;
            font-size: 10px;
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        @media print {
            .relatorio { box-shadow: none; }
            .header, .footer { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatórios</h1>
    </div>
    <div class="meta">
        Gerado em: {{ $data_geracao }}<br>
        Sistema: {{ $nome_sistema }}
    </div>
    @foreach($relatorios as $relatorio)
        <div class="relatorio">
            <div class="titulo">{{ $relatorio->titulo }}</div>
            <div class="info">
                <b>Responsável:</b> {{ $relatorio->nome_responsavel ?? '-' }}<br>
                <b>Status:</b> {{ $relatorio->status }}<br>
                <b>Progresso:</b> {{ $relatorio->progresso }}%<br>
                <b>Data:</b> {{ \Carbon\Carbon::parse($relatorio->date_created ?? $relatorio->created_at)->format('d/m/Y') }}
            </div>
            <div class="detalhes">
                {!! nl2br(e($relatorio->detalhes)) !!}
            </div>
            @if(isset($relatorio->images) && is_array($relatorio->images) && count($relatorio->images))
                <div style="margin-top:12px;">
                    <b>Imagens:</b><br>
                    @foreach($relatorio->images as $img)
                        <img src="{{ public_path('storage/' . ($img['thumb'] ?? $img['path'])) }}" style="max-width:120px; max-height:90px; margin:2px; border:1px solid #ccc; border-radius:4px;">
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
    <div class="footer">
        PDF gerado automaticamente pelo sistema em {{ $data_geracao }}
    </div>
</body>
</html> 