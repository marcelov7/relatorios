<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EquipamentoTest;

class TestSetoresEquipamentoTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:setores-equipamento-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa os setores únicos dos equipamentos de teste';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testando setores únicos dos equipamentos de teste...');

        // Buscar todos os setores
        $setores = EquipamentoTest::select('setor')
            ->whereNotNull('setor')
            ->where('setor', '!=', '')
            ->distinct()
            ->orderBy('setor')
            ->pluck('setor')
            ->filter()
            ->values();

        $this->info("Total de setores únicos: {$setores->count()}");
        
        if ($setores->count() > 0) {
            $this->info("\nSetores encontrados:");
            foreach ($setores as $setor) {
                $this->info("- {$setor}");
                
                // Contar equipamentos por setor
                $count = EquipamentoTest::where('setor', $setor)->count();
                $this->info("  Equipamentos: {$count}");
            }
        } else {
            $this->warn('Nenhum setor encontrado nos equipamentos de teste.');
        }

        // Testar filtro de relatórios por setor
        $this->info("\nTestando filtro de relatórios por setor:");
        foreach ($setores as $setor) {
            $relatoriosCount = \App\Models\Relatorio::whereHas('equipamentosTeste', function($q) use ($setor) {
                $q->where('setor', $setor);
            })->count();
            
            $this->info("Setor '{$setor}': {$relatoriosCount} relatórios");
        }

        $this->info('Teste concluído!');
    }
} 