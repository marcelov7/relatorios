<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class Setor extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'setores';

    protected $fillable = [
        'tenant_id',
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function equipamentos()
    {
        return $this->belongsToMany(Equipamento::class, 'setor_equipamento');
    }
} 