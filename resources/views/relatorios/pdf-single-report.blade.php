<div class="relatorio-card">
    <div class="relatorio-title">
        {{ $relatorio->titulo ?? 'Relatório #' . $relatorio->id }}
    </div>
    
    <table class="info-grid">
        <tr><td class="info-item">
            <span class="info-label">Responsável:</span><br>
            {{ $relatorio->nome_responsavel ?? 'Não informado' }}
        </td><td class="info-item">
            <span class="info-label">Cargo:</span><br>
            {{ $relatorio->cargo_responsavel ?? 'Não informado' }}
        </td></tr><tr><td class="info-item">
            <span class="info-label">Status:</span><br>
            <span class="status-badge status-{{ strtolower(str_replace(' ', '', $relatorio->status)) }}">
                {{ $relatorio->status }}
            </span>
        </td><td class="info-item">
            <span class="info-label">Progresso:</span><br>
            {{ $relatorio->progresso ?? 0 }}%
        </td></tr><tr><td class="info-item">
            <span class="info-label">Data Criação:</span><br>
            {{ \Carbon\Carbon::parse($relatorio->date_created ?? $relatorio->created_at)->format('d/m/Y') }}
            @if($relatorio->time_created)
                às {{ $relatorio->time_created }}
            @endif
        </td><td class="info-item">
            <span class="info-label">Atividade:</span><br>
            {{ $relatorio->activity ?? 'Não especificada' }}
        </td></tr>
    </table>
    
    @if($relatorio->equipamentosTeste && $relatorio->equipamentosTeste->count() > 0)
    <div class="equipamentos-section">
        <div class="detalhes-title">Equipamentos:</div>
        @foreach($relatorio->equipamentosTeste as $equipamento)
        <div class="equipamento-item">
            <strong>{{ $equipamento->tag ?? $equipamento->nome }}</strong>
            @if($equipamento->setor)
                - Setor: {{ $equipamento->setor }}
            @endif
            @if($equipamento->status)
                - Status: {{ $equipamento->status }}
            @endif
        </div>
        @endforeach
    </div>
    @endif
    
    @if($relatorio->detalhes)
    <div class="detalhes-section">
        <div class="detalhes-title">Detalhes:</div>
        @php
            $detalhesPdf = preg_replace('/<br\\s*\\/?\\s*>/i', "\n", $relatorio->detalhes);
            $detalhesPdf = strip_tags($detalhesPdf);
        @endphp
        <div class="detalhes-content">{!! nl2br(e($detalhesPdf)) !!}</div>
    </div>
    @endif
    
    @if($relatorio->imagens && $relatorio->imagens->isNotEmpty())
    <div class="images-section">
        <div class="detalhes-title">Imagens ({{ $relatorio->imagens->count() }}):</div>
        <table class="image-grid">
            @foreach($relatorio->imagens->chunk(3) as $linha)
            <tr>
            @foreach($linha as $imagem)
            <td class="image-item">
                @php
                    // Tentar diferentes caminhos para a imagem
                    $imageDataUri = null;
                    $possiblePaths = [$imagem->caminho_medium, $imagem->caminho_thumb, $imagem->caminho_original];
                    
                    foreach ($possiblePaths as $path) {
                        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                            $mime = ($path === $imagem->caminho_medium || $path === $imagem->caminho_thumb)
                                ? 'image/jpeg'
                                : ($imagem->mime_type ?: 'image/jpeg');
                            $imageDataUri = 'data:' . $mime . ';base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($path));
                            break;
                        }
                    }
                @endphp
                
                @if($imageDataUri)
                    <img src="{{ $imageDataUri }}" alt="{{ $imagem->nome_original }}">
                @else
                    <div style="width: 150px; height: 120px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; border-radius: 4px;">
                        <span style="color: #999; font-size: 11px;">Imagem não encontrada</span>
                    </div>
                @endif
                
                @if($imagem->nome_original)
                <div class="image-caption">{{ $imagem->nome_original }}</div>
                @endif
            </td>
            @endforeach
            @for($vazias = $linha->count(); $vazias < 3; $vazias++)
                <td class="image-item"></td>
            @endfor
            </tr>
            @endforeach
        </table>
    </div>
    @endif
    
    @if($relatorio->atualizacoes && $relatorio->atualizacoes->count() > 0)
    <div class="detalhes-section">
        <div class="detalhes-title">Histórico de Atualizações ({{ $relatorio->atualizacoes->count() }}):</div>
        @foreach($relatorio->atualizacoes->take(3) as $atualizacao)
        <div style="margin: 8px 0; padding: 8px; background: #f8f9fa; border-left: 3px solid #1976d2; font-size: 11px;">
            <strong>{{ $atualizacao->created_at->format('d/m/Y H:i') }}</strong>
            @if($atualizacao->usuario)
                por {{ $atualizacao->usuario->name }}
            @endif
            <br>
            <div style="margin-top: 4px;">{{ $atualizacao->descricao ?? 'Atualização registrada' }}</div>
        </div>
        @endforeach
        @if($relatorio->atualizacoes->count() > 3)
        <div style="font-size: 10px; color: #666; margin-top: 8px;">
            ... e mais {{ $relatorio->atualizacoes->count() - 3 }} atualizações
        </div>
        @endif
    </div>
    @endif
</div>
