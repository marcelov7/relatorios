<div class="relatorio-card">
    <div class="relatorio-title">
        {{ $relatorio->titulo ?? 'Relatório #' . $relatorio->id }}
    </div>
    
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Responsável:</span><br>
            {{ $relatorio->nome_responsavel ?? 'Não informado' }}
        </div>
        <div class="info-item">
            <span class="info-label">Cargo:</span><br>
            {{ $relatorio->cargo_responsavel ?? 'Não informado' }}
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span><br>
            <span class="status-badge status-{{ strtolower(str_replace(' ', '', $relatorio->status)) }}">
                {{ $relatorio->status }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Progresso:</span><br>
            {{ $relatorio->progresso ?? 0 }}%
        </div>
        <div class="info-item">
            <span class="info-label">Data Criação:</span><br>
            {{ \Carbon\Carbon::parse($relatorio->date_created ?? $relatorio->created_at)->format('d/m/Y') }}
            @if($relatorio->time_created)
                às {{ $relatorio->time_created }}
            @endif
        </div>
        <div class="info-item">
            <span class="info-label">Atividade:</span><br>
            {{ $relatorio->activity ?? 'Não especificada' }}
        </div>
    </div>
    
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
        <div class="detalhes-content">{{ $relatorio->detalhes }}</div>
    </div>
    @endif
    
    @if(isset($relatorio->images) && is_array($relatorio->images) && count($relatorio->images) > 0)
    <div class="images-section">
        <div class="detalhes-title">Imagens ({{ count($relatorio->images) }}):</div>
        <div class="image-grid">
            @foreach($relatorio->images as $index => $img)
            <div class="image-item">
                @php
                    // Tentar diferentes caminhos para a imagem
                    $imagePath = null;
                    $possiblePaths = [
                        public_path('storage/' . ($img['thumb'] ?? $img['path'] ?? '')),
                        public_path('storage/' . ($img['medium'] ?? $img['path'] ?? '')),
                        public_path('storage/' . ($img['path'] ?? '')),
                        storage_path('app/public/' . ($img['thumb'] ?? $img['path'] ?? '')),
                        storage_path('app/public/' . ($img['medium'] ?? $img['path'] ?? '')),
                        storage_path('app/public/' . ($img['path'] ?? '')),
                    ];
                    
                    foreach ($possiblePaths as $path) {
                        if ($path && file_exists($path)) {
                            $imagePath = $path;
                            break;
                        }
                    }
                @endphp
                
                @if($imagePath)
                    <img src="file://{{ $imagePath }}" alt="Imagem {{ $index + 1 }}" loading="lazy">
                @else
                    <div style="width: 150px; height: 120px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd; border-radius: 4px;">
                        <span style="color: #999; font-size: 11px;">Imagem não encontrada</span>
                    </div>
                @endif
                
                @if(isset($img['original_name']))
                <div class="image-caption">{{ $img['original_name'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
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
