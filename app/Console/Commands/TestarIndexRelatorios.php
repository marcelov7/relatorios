<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;
use Carbon\Carbon;

class TestarIndexRelatorios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testar:index-relatorios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se as datas estão sendo exibidas corretamente no Index de relatórios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $relatorios = Relatorio::latest()->take(3)->get();

        $this->info('📋 Testando exibição de datas no Index de relatórios:');
        $this->info('');

        foreach ($relatorios as $relatorio) {
            $this->info("🔍 Relatório #{$relatorio->id}: {$relatorio->titulo}");
            $this->info("   📅 Data do relato: " . ($relatorio->date_created ? Carbon::parse($relatorio->date_created)->format('d/m/Y') : 'Não informada') . 
                       ($relatorio->time_created ? ' às ' . $relatorio->time_created : ''));
            $this->info("   🗓️  Criado em: " . $relatorio->created_at->format('d/m/Y'));
            $this->info("   👤 Responsável: " . ($relatorio->nome_responsavel ?: ($relatorio->autor ? $relatorio->autor->name : 'Não informado')));
            $this->info("   📊 Status: {$relatorio->status} ({$relatorio->progresso}%)");
            $this->info('');
        }

        $this->info('✅ Teste concluído! Verifique se as datas estão sendo exibidas corretamente no Index.');
        $this->info('');
        $this->info('📝 Campos que devem aparecer no card do Index:');
        $this->info('   • Título do relatório');
        $this->info('   • Status');
        $this->info('   • Responsável');
        $this->info('   • Equipamentos');
        $this->info('   • Progresso');
        $this->info('   • Local (se informado)');
        $this->info('   • Data do relato (date_created + time_created)');
        $this->info('   • Criado em (created_at)');
        $this->info('   • Ações (Ver Detalhes, Editar, Excluir)');
        $this->info('   • Indicadores (fotos, histórico)');
    }
} 
