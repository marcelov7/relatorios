<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'motores';

    protected $fillable = [
        'tag',
        'equipamento',
        'carcaca_fabricante',
        'potencia_kw',
        'potencia_cv',
        'rotacao',
        'corrente_placa',
        'corrente_configurada',
        'tipo_equipamento_modelo',
        'fabricante',
        'reserva_almox',
        'local',
        'foto',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'potencia_kw' => 'decimal:2',
        'potencia_cv' => 'decimal:2',
        'rotacao' => 'integer',
        'corrente_placa' => 'decimal:2',
        'corrente_configurada' => 'decimal:2',
        'ativo' => 'boolean',
    ];





    /**
     * Scope para motores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para motores em reserva no almoxarifado
     */
    public function scopeReservaAlmox($query)
    {
        return $query->whereNotNull('reserva_almox')->where('reserva_almox', '!=', '');
    }



    /**
     * Scope por fabricante
     */
    public function scopePorFabricante($query, $fabricante)
    {
        return $query->where('fabricante', 'like', "%{$fabricante}%");
    }

    /**
     * Accessor para nome completo com tag
     */
    public function getNomeCompletoAttribute()
    {
        return "{$this->tag} - {$this->equipamento}";
    }

    /**
     * Accessor para reserva no almoxarifado com cor
     */
    public function getReservaAlmoxClassAttribute()
    {
        return !empty($this->reserva_almox)
            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
    }

    /**
     * Accessor para tipo de equipamento/modelo com cor
     */
    public function getTipoEquipamentoModeloClassAttribute()
    {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
    }

    /**
     * Verificar se está em reserva no almoxarifado
     */
    public function getEstaReservaAlmoxAttribute()
    {
        return !empty($this->reserva_almox);
    }
} 