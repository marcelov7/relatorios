<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'documento',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function relatorios()
    {
        return $this->hasMany(Relatorio::class);
    }

    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }

    public function setores()
    {
        return $this->hasMany(Setor::class);
    }

    public function motores()
    {
        return $this->hasMany(Motor::class);
    }

    public function inspecaoGeradores()
    {
        return $this->hasMany(InspecaoGerador::class);
    }
}
