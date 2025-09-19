<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ImageUploadService;

class RelatorioImagem extends Model
{
    protected $table = 'relatorio_imagens';

    protected $fillable = [
        'relatorio_id',
        'nome_original',
        'nome_arquivo',
        'caminho_original',
        'caminho_thumb',
        'caminho_medium',
        'mime_type',
        'tamanho',
        'ordem',
    ];

    protected $casts = [
        'tamanho' => 'integer',
        'ordem' => 'integer',
    ];

    /**
     * Relacionamento com o relatório
     */
    public function relatorio(): BelongsTo
    {
        return $this->belongsTo(Relatorio::class);
    }

    /**
     * Retorna URL da imagem original
     */
    public function getOriginalUrlAttribute(): string
    {
        $service = new ImageUploadService();
        return $service->getImageUrl($this->caminho_original);
    }

    /**
     * Retorna URL do thumbnail
     */
    public function getThumbUrlAttribute(): string
    {
        if (!$this->caminho_thumb) {
            return $this->original_url;
        }

        $service = new ImageUploadService();
        return $service->getImageUrl($this->caminho_thumb, $this->caminho_original);
    }

    /**
     * Retorna URL da imagem média
     */
    public function getMediumUrlAttribute(): string
    {
        if (!$this->caminho_medium) {
            return $this->original_url;
        }

        $service = new ImageUploadService();
        return $service->getImageUrl($this->caminho_medium, $this->caminho_original);
    }

    /**
     * Retorna tamanho formatado
     */
    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Remove a imagem e seus arquivos
     */
    public function deleteWithFiles(): bool
    {
        $service = new ImageUploadService();
        $deleted = $service->deleteImage($this->toArray());
        
        if ($deleted) {
            return $this->delete();
        }

        return false;
    }

    /**
     * Scope para ordenar por ordem
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordem', 'asc');
    }
} 