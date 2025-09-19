<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioAtualizacao extends Model
{
    use HasFactory;

    protected $table = 'relatorio_atualizacoes';

    protected $fillable = [
        'relatorio_id',
        'user_id',
        'progresso_anterior',
        'progresso_novo',
        'status_anterior',
        'status_novo',
        'descricao',
        'imagens'
    ];

    protected $casts = [
        'imagens' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Remover o accessor problemático que estava causando erro no Carbon::parse
    // public function getCreatedAtAttribute($value)
    // {
    //     return \Carbon\Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y');
    // }

    // Relacionamentos
    public function relatorio()
    {
        return $this->belongsTo(Relatorio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
