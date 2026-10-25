<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Traits\BelongsToTenant;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'setor',
        'cargo',
        'telefone',
        'role',
        'can_edit_enderecos',
        'can_delete_enderecos',
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
            'can_edit_enderecos' => 'boolean',
            'can_delete_enderecos' => 'boolean',
        ];
    }

    /**
     * Verifica se o usuário é super admin global
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Verifica se o usuário é administrador (do tenant ou super admin)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Verifica se o usuário tem permissão para criar/editar endereços lógicos
     */
    public function canEditEnderecos(): bool
    {
        return $this->isAdmin() || (bool) $this->can_edit_enderecos;
    }

    /**
     * Verifica se o usuário tem permissão para excluir endereços lógicos
     */
    public function canDeleteEnderecos(): bool
    {
        return $this->isAdmin() || (bool) $this->can_delete_enderecos;
    }

    /**
     * Relatórios criados pelo usuário
     */
    public function relatorios()
    {
        return $this->hasMany(Relatorio::class, 'autor_id');
    }

    /**
     * Relatórios criados pelo usuário (alias para relatorios)
     */
    public function relatoriosCriados()
    {
        return $this->hasMany(Relatorio::class, 'autor_id');
    }
}
