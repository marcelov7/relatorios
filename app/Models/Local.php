<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    protected $fillable = [
        'nome',
        'descricao',
        'setor',
        'responsavel',
        'telefone',
        'email',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento: Um local tem muitos equipamentos
     */
    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }

    /**
     * Relacionamento: Um local pode ter muitos relatórios
     */
    public function relatorios()
    {
        return $this->hasMany(Relatorio::class);
    }

    /**
     * Scope para locais ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Accessor para nome formatado
     */
    public function getNomeCompletoAttribute()
    {
        return $this->setor ? "{$this->nome} - {$this->setor}" : $this->nome;
    }
}
