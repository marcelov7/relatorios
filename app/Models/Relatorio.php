<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Relatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'setor_id',
        'activity',
        'user_id',
        'autor_id',
        'nome_responsavel',
        'cargo_responsavel',
        'date_created',
        'time_created',
        'local_id',
        'status',
        'progresso',
        'detalhes'
    ];

    protected $casts = [
        'date_created' => 'date',
        'time_created' => 'datetime:H:i',
        'progresso' => 'integer'
    ];

    /**
     * Relacionamento: Um relatório pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Um relatório foi criado por um autor (usuário)
     */
    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Relacionamento: Um relatório pertence a um setor
     */
    public function setor()
    {
        return $this->belongsTo(\App\Models\Setor::class, 'setor_id');
    }

    public function atualizacoes()
    {
        return $this->hasMany(RelatorioAtualizacao::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com imagens do relatório
     */
    public function imagens()
    {
        return $this->hasMany(RelatorioImagem::class)->ordered();
    }

    // Verifica se o usuário pode atualizar este relatório
    public function podeSerAtualizadoPor($user)
    {
        // Se já está 100% completo, não pode ser atualizado
        if ($this->progresso >= 100) {
            return false;
        }

        // Qualquer usuário pode atualizar relatórios com progresso de 0 a 99
        return true;
    }

    /**
     * Relacionamento: Um relatório pode pertencer a um local
     */
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    /**
     * Relacionamento: Um relatório pode ter muitos equipamentos (many-to-many)
     */
    public function equipamentos()
    {
        return $this->belongsToMany(Equipamento::class, 'relatorio_equipamento');
    }

    /**
     * Relacionamento: Um relatório pode ter muitos equipamentos de teste (many-to-many)
     */
    public function equipamentosTeste()
    {
        return $this->belongsToMany(\App\Models\EquipamentoTest::class, 'relatorio_equipamento_test', 'relatorio_id', 'equipamento_test_id');
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por setor
     */
    public function scopeSetor($query, $setor)
    {
        return $query->where('sector', 'like', "%{$setor}%");
    }

    /**
     * Scope para filtrar por local
     */
    public function scopePorLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Scope para busca geral
     */
    public function scopeBusca($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('titulo', 'like', "%{$termo}%")
              ->orWhere('sector', 'like', "%{$termo}%")
              ->orWhere('activity', 'like', "%{$termo}%")
              ->orWhere('detalhes', 'like', "%{$termo}%");
        });
    }

    /**
     * Accessor para classe CSS do status
     */
    public function getStatusClassAttribute()
    {
        return match($this->status) {
            'Aberta' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            'Em Andamento' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'Concluída' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'Cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        };
    }

    /**
     * Accessor para classe CSS do progresso
     */
    public function getProgressoClassAttribute()
    {
        if ($this->progresso >= 100) {
            return 'bg-green-500';
        } elseif ($this->progresso >= 75) {
            return 'bg-blue-500';
        } elseif ($this->progresso >= 50) {
            return 'bg-yellow-500';
        } elseif ($this->progresso >= 25) {
            return 'bg-orange-500';
        } else {
            return 'bg-red-500';
        }
    }

    /**
     * Accessor para identificação completa
     */
    public function getIdentificacaoCompletaAttribute()
    {
        $parts = [$this->titulo];
        
        if ($this->local) {
            $parts[] = "Local: {$this->local->nome}";
        }
        
        return implode(' - ', $parts);
    }
}
