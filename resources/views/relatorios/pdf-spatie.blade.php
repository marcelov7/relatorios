<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios Técnicos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 0; }
        h1, h2, h3 { color: #1e40af; margin-bottom: 8px; }
        .relatorio { margin-bottom: 40px; border-bottom: 2px solid #e5e7eb; padding-bottom: 30px; }
        .tabela-info { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .tabela-info th, .tabela-info td { border: 1px solid #d1d5db; padding: 6px 10px; }
        .tabela-info th { background: #f1f5f9; text-align: left; }
        .imagens-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .imagem-item { border: 1px solid #d1d5db; border-radius: 4px; overflow: hidden; width: 160px; }
        .imagem-item img { width: 100%; height: auto; display: block; }
        .campo-label { font-weight: bold; color: #1e40af; }
        .secao { margin-bottom: 18px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Relatórios Técnicos</h1>
    <p><strong>Gerado em:</strong> {{ $data_geracao }} | <strong>Sistema:</strong> {{ $nome_sistema }}</p>
    @foreach($relatorios as $relatorio)
        <div class="relatorio">
            <h2>{{ $relatorio->titulo }}</h2>
            <div class="secao">
                <span class="campo-label">Atividade:</span> {{ $relatorio->activity }}<br>
                <span class="campo-label">Tipo de Atividade:</span> {{ $relatorio->tipo_atividade ?? 'N/A' }}<br>
                <span class="campo-label">Responsável:</span> {{ $relatorio->nome_responsavel }} - {{ $relatorio->cargo_responsavel }}<br>
                <span class="campo-label">Status:</span> {{ $relatorio->status }}<br>
                <span class="campo-label">Progresso:</span> {{ $relatorio->progresso }}%<br>
                <span class="campo-label">Data de Criação:</span> {{ \Carbon\Carbon::parse($relatorio->created_at)->format('d/m/Y H:i') }}<br>
                <span class="campo-label">Autor:</span> {{ $relatorio->autor->name ?? 'N/A' }}<br>
                <span class="campo-label">Local:</span> {{ $relatorio->local->nome ?? 'N/A' }}<br>
                <span class="campo-label">Setor:</span> {{ $relatorio->setor->nome ?? 'N/A' }}<br>
            </div>
            <div class="secao">
                <span class="campo-label">Equipamentos Relacionados:</span>
                @if($relatorio->equipamentosTeste && count($relatorio->equipamentosTeste) > 0)
                    <table class="tabela-info">
                        <thead>
                            <tr>
                                <th>TAG</th>
                                <th>Nome</th>
                                <th>Setor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatorio->equipamentosTeste as $equip)
                                <tr>
                                    <td>{{ $equip->tag ?? 'N/A' }}</td>
                                    <td>{{ $equip->nome ?? 'N/A' }}</td>
                                    <td>{{ $equip->setor ?? 'N/A' }}</td>
                                    <td>{{ $equip->status ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    Nenhum equipamento relacionado.
                @endif
            </div>
            <div class="secao">
                <span class="campo-label">Descrição da Atividade:</span>
                <div style="white-space: pre-wrap; margin-top: 6px;">{!! $relatorio->detalhes !!}</div>
            </div>
            @if(is_array($relatorio->images) && count($relatorio->images) > 0)
                <div class="secao">
                    <span class="campo-label">Imagens do Relatório:</span>
                    <div class="imagens-grid">
                        @foreach($relatorio->images as $img)
                            <div class="imagem-item">
                                <img src="{{ public_path('storage/' . $img['path']) }}" alt="Imagem do relatório">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($relatorio->atualizacoes && count($relatorio->atualizacoes) > 0)
                <div class="secao">
                    <span class="campo-label">Histórico de Atualizações:</span>
                    <table class="tabela-info">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Usuário</th>
                                <th>Descrição</th>
                                <th>Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatorio->atualizacoes as $atual)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($atual->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $atual->usuario->name ?? 'N/A' }}</td>
                                    <td>{{ $atual->descricao ?? '-' }}</td>
                                    <td>{{ $atual->progresso_novo ?? '-' }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html> 