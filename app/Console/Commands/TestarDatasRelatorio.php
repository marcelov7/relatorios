<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Carbon\Carbon;

class TestarDatasRelatorio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testar:datas-relatorio {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se as datas estão sendo exibidas corretamente no card do relatório';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        
        if ($id) {
            $relatorio = Relatorio::find($id);
            if (!$relatorio) {
                $this->error("❌ Relatório com ID {$id} não encontrado!");
                return;
            }
            $relatorios = collect([$relatorio]);
        } else {
            $relatorios = Relatorio::latest()->take(3)->get();
        }

        $this->info('📋 Testando exibição de datas nos relatórios:');
        $this->info('');

        foreach ($relatorios as $relatorio) {
            $this->info("🔍 Relatório #{$relatorio->id}: {$relatorio->titulo}");
            $this->info("   📅 Data e hora do relato: " . ($relatorio->date_created ? Carbon::parse($relatorio->date_created)->format('d/m/Y') : 'Não informada') . 
                       ($relatorio->time_created ? ' às ' . $relatorio->time_created : ''));
            $this->info("   🗓️  Data de criação no sistema: " . $relatorio->created_at->format('d/m/Y H:i:s'));
            $this->info("   👤 Responsável: " . ($relatorio->nome_responsavel ?: ($relatorio->autor ? $relatorio->autor->name : 'Não informado')));
            $this->info("   📊 Status: {$relatorio->status} ({$relatorio->progresso}%)");
            $this->info('');
        }

        $this->info('✅ Teste concluído! Verifique se as datas estão sendo exibidas corretamente no card.');
        $this->info('');
        $this->info('📝 Campos que devem aparecer no card:');
        $this->info('   • Atividade');
        $this->info('   • Responsável');
        $this->info('   • Data e hora do relato');
        $this->info('   • Data de criação');
    }
} 