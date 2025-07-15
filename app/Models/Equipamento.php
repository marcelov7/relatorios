<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_tag',
        'nome',
        'descricao',
        'local_id',
        'tipo',
        'marca',
        'modelo',
        'numero_serie',
        'data_instalacao',
        'data_ultima_manutencao',
        'proxima_manutencao',
        'status',
        'observacoes',
        'ativo',
        'setor_id',
    ];

    protected $casts = [
        'data_instalacao' => 'date',
        'data_ultima_manutencao' => 'date',
        'proxima_manutencao' => 'date',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento: Um equipamento pertence a um local
     */
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    /**
     * Relacionamento: Um equipamento pode ter muitos relatórios (many-to-many)
     */
    public function relatorios()
    {
        return $this->belongsToMany(Relatorio::class, 'relatorio_equipamento');
    }

    /**
     * Relacionamento: Um equipamento pertence a um setor
     */
    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Relacionamento: Um equipamento pode pertencer a vários setores
     */
    public function setores()
    {
        return $this->belongsToMany(Setor::class, 'setor_equipamento');
    }

    /**
     * Scope para equipamentos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para equipamentos operacionais
     */
    public function scopeOperacionais($query)
    {
        return $query->where('status', 'Operacional');
    }

    /**
     * Scope por local
     */
    public function scopePorLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Accessor para nome completo com tag
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->equipment_tag} - {$this->nome}";
    }

    /**
     * Accessor para status com cor
     */
    public function getStatusClassAttribute()
    {
        return match($this->status) {
            'Operacional' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'Manutenção' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'Inativo' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
            'Defeito' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        };
    }

    /**
     * Verificar se precisa de manutenção
     */
    public function getPrecisaManutencaoAttribute()
    {
        if (!$this->proxima_manutencao) {
            return false;
        }
        
        return $this->proxima_manutencao <= now()->addDays(7);
    }
}
