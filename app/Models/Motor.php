<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag',
        'equipamento',
        'carcaca',
        'potencia_kw',
        'potencia_cv',
        'rotacao',
        'corrente_placa',
        'corrente_configurada',
        'tipo_equipamento',
        'fabricante',
        'reserva_almox',
        'local_id',
        'foto',
        'armazenamento',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'potencia_kw' => 'decimal:2',
        'potencia_cv' => 'decimal:2',
        'rotacao' => 'integer',
        'corrente_placa' => 'decimal:2',
        'corrente_configurada' => 'decimal:2',
        'reserva_almox' => 'boolean',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento: Um motor pertence a um local
     */
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    /**
     * Scope para motores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para motores instalados
     */
    public function scopeInstalados($query)
    {
        return $query->where('armazenamento', 'Instalado');
    }

    /**
     * Scope para motores em almoxarifado
     */
    public function scopeAlmoxarifado($query)
    {
        return $query->where('armazenamento', 'Almoxarifado');
    }

    /**
     * Scope para motores em manutenção
     */
    public function scopeManutencao($query)
    {
        return $query->where('armazenamento', 'Manutenção');
    }

    /**
     * Scope por local
     */
    public function scopePorLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Scope por fabricante
     */
    public function scopePorFabricante($query, $fabricante)
    {
        return $query->where('fabricante', 'like', "%{$fabricante}%");
    }

    /**
     * Scope por tipo de equipamento
     */
    public function scopePorTipoEquipamento($query, $tipo)
    {
        return $query->where('tipo_equipamento', 'like', "%{$tipo}%");
    }

    /**
     * Accessor para nome completo com tag
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->tag} - {$this->equipamento}";
    }

    /**
     * Accessor para potência formatada
     */
    public function getPotenciaFormatadaAttribute()
    {
        $potencia = [];
        if ($this->potencia_kw) {
            $potencia[] = "{$this->potencia_kw} kW";
        }
        if ($this->potencia_cv) {
            $potencia[] = "{$this->potencia_cv} CV";
        }
        return implode(' / ', $potencia) ?: 'N/A';
    }

    /**
     * Accessor para corrente formatada
     */
    public function getCorrenteFormatadaAttribute()
    {
        $corrente = [];
        if ($this->corrente_placa) {
            $corrente[] = "Placa: {$this->corrente_placa}A";
        }
        if ($this->corrente_configurada) {
            $corrente[] = "Config: {$this->corrente_configurada}A";
        }
        return implode(' / ', $corrente) ?: 'N/A';
    }

    /**
     * Accessor para armazenamento com cor
     */
    public function getArmazenamentoClassAttribute()
    {
        return match($this->armazenamento) {
            'Instalado' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'Almoxarifado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            'Manutenção' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'Descartado' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        };
    }

    /**
     * Accessor para reserva almox com cor
     */
    public function getReservaAlmoxClassAttribute()
    {
        return $this->reserva_almox 
            ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400'
            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
    }

    /**
     * Accessor para URL da foto
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }
        
        return asset('storage/' . $this->foto);
    }

    /**
     * Mutator para converter tag para maiúsculas
     */
    public function setTagAttribute($value)
    {
        $this->attributes['tag'] = strtoupper($value);
    }

    /**
     * Mutator para converter equipamento para title case
     */
    public function setEquipamentoAttribute($value)
    {
        $this->attributes['equipamento'] = ucwords(strtolower($value));
    }

    /**
     * Mutator para converter fabricante para title case
     */
    public function setFabricanteAttribute($value)
    {
        $this->attributes['fabricante'] = ucwords(strtolower($value));
    }
}
