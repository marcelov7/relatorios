<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class EquipamentoTest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'tag',
        'nome',
        'setor',
        'status',
    ];
}
