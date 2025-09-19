<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class InspecaoGerador extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'inspecao_geradores';

    protected $fillable = [
        'data_inspecao',
        'colaborador_id',
        'nivel_oleo_motor_parado',
        'nivel_agua_parado',
        'sync_gerador',
        'sync_rede',
        'temperatura_agua',
        'pressao_oleo',
        'frequencia',
        'tensao_a',
        'tensao_b',
        'tensao_c',
        'rpm_1800',
        'tensao_bateria_parado',
        'tensao_alternador_marcha',
        'nivel_combustivel',
        'iluminacao_sala_deficiente',
        'limpeza_sala_realizada',
        'situacao',
        'observacoes',
        'user_id',
        'setor_id',
        'setor_text'
    ];

    protected $casts = [
        'data_inspecao' => 'datetime',
        'sync_gerador' => 'decimal:2',
        'sync_rede' => 'decimal:2',
        'temperatura_agua' => 'decimal:2',
        'pressao_oleo' => 'decimal:2',
        'frequencia' => 'decimal:2',
        'tensao_a' => 'decimal:2',
        'tensao_b' => 'decimal:2',
        'tensao_c' => 'decimal:2',
        'rpm_1800' => 'integer',
        'tensao_bateria_parado' => 'decimal:2',
        'tensao_alternador_marcha' => 'decimal:2',
        'nivel_combustivel' => 'integer',
        'iluminacao_sala_deficiente' => 'boolean',
        'limpeza_sala_realizada' => 'boolean',
        'colaborador_id' => 'integer',
        'setor_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Atributos adicionais que devem ser incluídos na serialização JSON
     */
    protected $appends = [
        'situacao_class',
        'situacao_icon',
        'situacao_color'
    ];

    /**
     * Relacionamento: Uma inspeção pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Uma inspeção pertence a um colaborador
     */
    public function colaborador()
    {
        return $this->belongsTo(User::class, 'colaborador_id');
    }

    /**
     * Relacionamento: Uma inspeção pertence a um setor
     */
    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Scope para filtrar por situação
     */
    public function scopeSituacao($query, $situacao)
    {
        return $query->where('situacao', $situacao);
    }

    /**
     * Scope para filtrar por setor
     */
    public function scopeSetor($query, $setorId)
    {
        return $query->where('setor_id', $setorId);
    }

    /**
     * Scope para busca
     */
    public function scopeBusca($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->whereHas('colaborador', function($subQ) use ($termo) {
                $subQ->where('name', 'like', "%{$termo}%");
            })
            ->orWhere('observacoes', 'like', "%{$termo}%")
            ->orWhere('situacao', 'like', "%{$termo}%");
        });
    }

    /**
     * Scope para filtrar por data
     */
    public function scopePorData($query, $dataInicio, $dataFim = null)
    {
        if ($dataFim) {
            return $query->whereBetween('data_inspecao', [$dataInicio, $dataFim]);
        }
        return $query->whereDate('data_inspecao', $dataInicio);
    }

    /**
     * Acessor para classe CSS da situação
     */
    public function getSituacaoClassAttribute()
    {
        return match($this->situacao) {
            'OK - PRONTO ( AUTO)' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'ANORMAL - PRECISA DE INSPEÇÃO' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
        };
    }

    /**
     * Acessor para ícone da situação
     */
    public function getSituacaoIconAttribute()
    {
        return match($this->situacao) {
            'OK - PRONTO ( AUTO)' => '✓',
            'ANORMAL - PRECISA DE INSPEÇÃO' => '✗',
            default => '?'
        };
    }

    /**
     * Acessor para cor da situação
     */
    public function getSituacaoColorAttribute()
    {
        return match($this->situacao) {
            'OK - PRONTO ( AUTO)' => 'green',
            'ANORMAL - PRECISA DE INSPEÇÃO' => 'red',
            default => 'gray'
        };
    }

    /**
     * Método para calcular situação automaticamente baseado nos valores
     */
    public function calcularSituacao()
    {
        $problemas = [];

        // Verificar níveis
        if ($this->nivel_oleo_motor_parado === 'MÍNIMO') {
            $problemas[] = 'Nível de óleo no mínimo';
        }

        if ($this->nivel_agua_parado === 'MÍNIMO') {
            $problemas[] = 'Nível de água no mínimo';
        }

        // Verificar pressão de óleo (3 a 6 bar)
        if ($this->pressao_oleo && ($this->pressao_oleo < 3 || $this->pressao_oleo > 6)) {
            $problemas[] = 'Pressão de óleo fora do padrão';
        }

        // Verificar frequência (57 a 63 Hz)
        if ($this->frequencia && ($this->frequencia < 57 || $this->frequencia > 63)) {
            $problemas[] = 'Frequência fora do padrão';
        }

        // Verificar tensões (210V a 240V)
        $tensoes = [$this->tensao_a, $this->tensao_b, $this->tensao_c];
        foreach ($tensoes as $tensao) {
            if ($tensao && ($tensao < 210 || $tensao > 240)) {
                $problemas[] = 'Tensão fora do padrão';
                break;
            }
        }

        // Verificar temperatura da água (20°C a 80°C)
        if ($this->temperatura_agua && ($this->temperatura_agua < 20 || $this->temperatura_agua > 80)) {
            $problemas[] = 'Temperatura da água fora do padrão';
        }

        // Verificar tensão da bateria (24V a 26V)
        if ($this->tensao_bateria_parado && ($this->tensao_bateria_parado < 24 || $this->tensao_bateria_parado > 26)) {
            $problemas[] = 'Tensão da bateria fora do padrão';
        }

        // Verificar tensão do alternador (24V a 28V)
        if ($this->tensao_alternador_marcha && ($this->tensao_alternador_marcha < 24 || $this->tensao_alternador_marcha > 28)) {
            $problemas[] = 'Tensão do alternador fora do padrão';
        }

        // Verificar nível de combustível (acima de 50%)
        if ($this->nivel_combustivel && $this->nivel_combustivel < 50) {
            $problemas[] = 'Nível de combustível baixo';
        }

        // Verificar iluminação e limpeza
        if ($this->iluminacao_sala_deficiente) {
            $problemas[] = 'Iluminação da sala deficiente';
        }

        if (!$this->limpeza_sala_realizada) {
            $problemas[] = 'Limpeza da sala não realizada';
        }

        // Definir situação baseada nos problemas encontrados
        if (empty($problemas)) {
            $this->situacao = 'OK - PRONTO ( AUTO)';
        } else {
            $this->situacao = 'ANORMAL - PRECISA DE INSPEÇÃO';
        }

        return $this->situacao;
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de salvar, calcular situação automaticamente
        static::saving(function ($inspecao) {
            $inspecao->calcularSituacao();
        });
    }

    /**
     * Prepare the model for serialization.
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Garantir que os relacionamentos sejam incluídos
        if ($this->relationLoaded('user')) {
            $array['user'] = $this->user ? $this->user->toArray() : null;
        }
        
        if ($this->relationLoaded('colaborador')) {
            $array['colaborador'] = $this->colaborador ? $this->colaborador->toArray() : null;
        }
        
        if ($this->relationLoaded('setor')) {
            $array['setor'] = $this->setor ? $this->setor->toArray() : null;
        }
        
        return $array;
    }
} 