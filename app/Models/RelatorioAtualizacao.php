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
        'imagens' => 'array'
    ];

    // Accessor para garantir que created_at seja serializado como data local (Y-m-d H:i:s)
    public function getCreatedAtAttribute($value)
    {
        // Força o timezone para America/Sao_Paulo e retorna a data no formato d/m/Y
        return \Carbon\Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y');
    }

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
