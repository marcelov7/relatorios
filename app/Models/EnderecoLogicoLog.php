<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoLogicoLog extends Model
{
    use HasFactory;

    protected $table = 'endereco_logico_logs';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_name',
        'user_email',
        'endereco_logico_id',
        'tag',
        'acao',
        'dados_anteriores',
        'dados_novos',
        'ip',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
