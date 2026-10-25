<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class EnderecoLogico extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'enderecos_logicos';

    protected $fillable = [
        'tenant_id',
        'tag',
        'descricao',
        'endereco',
        'area',
        'observacoes',
    ];

    /**
     * Scope para busca dinâmica em tag, descrição ou endereço
     */
    public function scopeBuscar($query, $termo)
    {
        if (empty($termo)) {
            return $query;
        }

        $termo = trim($termo);

        return $query->where(function ($q) use ($termo) {
            $q->where('tag', 'like', "%{$termo}%")
              ->orWhere('descricao', 'like', "%{$termo}%")
              ->orWhere('endereco', 'like', "%{$termo}%")
              ->orWhere('area', 'like', "%{$termo}%");
        });
    }
}
