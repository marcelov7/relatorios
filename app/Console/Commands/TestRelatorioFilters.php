<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use App\Models\Setor;
use App\Models\User;

class TestRelatorioFilters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:relatorio-filters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa os filtros de relatórios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testando filtros de relatórios...');

        // Teste 1: Contar total de relatórios
        $total = Relatorio::count();
        $this->info("Total de relatórios: {$total}");

        // Teste 2: Contar setores
        $setores = Setor::where('ativo', true)->count();
        $this->info("Total de setores ativos: {$setores}");

        // Teste 3: Contar autores
        $autores = User::whereHas('relatorios')->count();
        $this->info("Total de autores: {$autores}");

        // Teste 4: Testar filtro por setor
        $setor = \App\Models\EquipamentoTest::whereNotNull('setor')->first();
        if ($setor) {
            $relatoriosPorSetor = Relatorio::whereHas('equipamentosTeste', function($q) use ($setor) {
                $q->where('setor', $setor->setor);
            })->count();
            $this->info("Relatórios do setor '{$setor->setor}': {$relatoriosPorSetor}");
        }

        // Teste 5: Testar filtro por autor
        $autor = User::whereHas('relatorios')->first();
        if ($autor) {
            $relatoriosPorAutor = Relatorio::where('autor_id', $autor->id)->count();
            $this->info("Relatórios do autor '{$autor->name}': {$relatoriosPorAutor}");
        }

        $this->info('Teste concluído!');
    }
} 