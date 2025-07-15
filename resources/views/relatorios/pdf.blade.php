<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios Técnicos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .capa { text-align: center; margin-top: 120px; }
        .logo { width: 120px; margin-bottom: 20px; }
        .titulo-capa { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .subtitulo-capa { font-size: 18px; margin-bottom: 30px; }
        .data-capa { font-size: 14px; color: #555; }
        .header, .footer { width: 100%; text-align: center; position: fixed; }
        .header { top: 0; left: 0; right: 0; height: 60px; border-bottom: 1px solid #eee; }
        .footer { bottom: 0; left: 0; right: 0; height: 40px; border-top: 1px solid #eee; font-size: 11px; color: #888; }
        .relatorio { page-break-after: always; padding: 30px 20px 60px 20px; }
        .relatorio:last-child { page-break-after: avoid; }
        .titulo-relatorio { font-size: 20px; font-weight: bold; margin-bottom: 8px; }
        .campo { margin-bottom: 6px; }
        .imagens { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px; }
        .imagens img { max-width: 260px; max-height: 180px; border: 1px solid #ccc; border-radius: 4px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <!-- Capa -->
    <div class="capa">
        @if(file_exists($logo_path))
            <img src="{{ $logo_path }}" class="logo" alt="Logo" style="display:block; margin:0 auto 32px auto; max-width:180px; width:auto; height:auto;">
        @endif
        <div class="titulo-capa">Relatórios Técnicos</div>
        <div class="subtitulo-capa">Exportação personalizada</div>
        <div class="data-capa">Gerado em: {{ $data_geracao }}</div>
        <div class="data-capa">Sistema: {{ $nome_sistema }}</div>
    </div>
    <div style="page-break-after: always;"></div>

    <!-- Cabeçalho e rodapé fixos -->
    <div class="header">
        @if(file_exists($logo_path))
            <img src="{{ $logo_path }}" style="height:32px;vertical-align:middle;">&nbsp;
        @endif
        <span style="font-size:16px;font-weight:bold;">{{ $nome_sistema }}</span>
    </div>
    <div class="footer">
        <span>Página <span class="page-number"></span> | Gerado em {{ $data_geracao }}</span>
    </div>

    <!-- Relatórios -->
    @foreach($relatorios as $relatorio)
        <div class="relatorio">
            <div class="titulo-relatorio">{{ $relatorio->titulo }}</div>
            <div class="campo"><b>Atividade:</b> {{ $relatorio->activity }}</div>
            <div class="campo"><b>Responsável:</b> {{ $relatorio->nome_responsavel }}</div>
            @if($relatorio->equipamentosTeste && count($relatorio->equipamentosTeste) > 0)
                <div class="campo"><b>Equipamentos/ Setores:</b>
                    <div style="margin: 6px 0 0 0;">
                        @foreach($relatorio->equipamentosTeste as $eq)
                            <span style="display:inline-block; background:#f3f4f6; color:#222; border-radius:12px; padding:3px 10px; font-size:12px; margin:2px 4px 2px 0; border:1px solid #e5e7eb;">
                                {{ $eq->tag ?? '' }}{{ $eq->tag ? ' - ' : '' }}{{ $eq->nome ?? '' }}
                                @if($eq->setor)
                                    <span style="color:#6b7280; font-size:11px;">({{ $eq->setor }})</span>
                                @endif
                                @if($eq->status)
                                    <span style="color:#059669; font-size:11px; font-weight:500;">[{{ $eq->status }}]</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="campo"><b>Data de Criação:</b> {{ \Carbon\Carbon::parse($relatorio->date_created)->format('d/m/Y') }}</div>
            <div class="campo"><b>Status:</b> {{ $relatorio->status }}</div>
            <div class="campo"><b>Progresso:</b> {{ $relatorio->progresso }}%</div>
            <div class="campo"><b>Detalhes:</b> {!! $relatorio->detalhes !!}</div>
            @if(is_array($relatorio->images) && count($relatorio->images) > 0)
                <div class="campo"><b>Imagens:</b></div>
                <div class="imagens">
                    @foreach($relatorio->images as $img)
                        <img src="{{ public_path('storage/' . ($img['thumb'] ?? $img['path'])) }}" alt="Imagem do relatório">
                    @endforeach
                </div>
            @endif
            @if($relatorio->atualizacoes && count($relatorio->atualizacoes) > 0)
                <div class="historico">
                    <h4 style="margin-top:18px;margin-bottom:6px;font-size:15px;border-bottom:1px solid #eee;">Histórico de Atualizações</h4>
                    @foreach($relatorio->atualizacoes as $atualizacao)
                        <div class="atualizacao-box" style="border:1px solid #e5e7eb;border-radius:6px;padding:10px 12px;margin-bottom:10px;background:#fafbfc;">
                            <div><b>Data:</b> {{ \Carbon\Carbon::parse($atualizacao->created_at)->format('d/m/Y H:i') }}</div>
                            <div><b>Usuário:</b> {{ $atualizacao->usuario->name ?? '-' }}</div>
                            <div><b>Progresso:</b> {{ $atualizacao->progresso_anterior }}% → {{ $atualizacao->progresso_novo }}%</div>
                            <div><b>Status:</b> {{ $atualizacao->status_anterior }} → {{ $atualizacao->status_novo }}</div>
                            <div><b>Descrição:</b> {{ $atualizacao->descricao }}</div>
                            @if(is_array($atualizacao->imagens) && count($atualizacao->imagens) > 0)
                                <div style="margin:12px 0 0 0; width:100%;">
                                    @foreach($atualizacao->imagens as $i => $img)
                                        @if($i > 0 && $i % 3 === 0)
                                            <br/>
                                        @endif
                                        <img src="{{ public_path('storage/' . ($img['thumb'] ?? $img['path'])) }}" alt="Imagem atualização"
                                             style="display:inline-block; max-width:100px; max-height:80px; border:1px solid #ccc; border-radius:4px; margin:0 8px 8px 0; vertical-align:top;" />
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</body>
</html> 