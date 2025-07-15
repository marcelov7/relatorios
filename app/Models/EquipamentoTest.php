<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipamentoTest extends Model
{
    protected $fillable = [
        'tag',
        'nome',
        'setor',
        'status',
    ];
}
