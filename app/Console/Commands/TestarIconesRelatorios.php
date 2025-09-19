<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;

class TestarIconesRelatorios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testar:icones-relatorios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se os ícones de fotos e histórico estão sendo calculados corretamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testando ícones de relatórios...');

        $relatorios = Relatorio::with(['equipamentosTeste', 'atualizacoes'])->take(5)->get();

        if ($relatorios->isEmpty()) {
            $this->error('❌ Nenhum relatório encontrado!');
            return;
        }

        $this->info('📊 Relatórios encontrados: ' . $relatorios->count());
        $this->info('');

        foreach ($relatorios as $relatorio) {
            $this->info("📋 Relatório: {$relatorio->titulo}");
            
            // Calcular total de fotos
            $totalFotos = 0;
            if (is_array($relatorio->images)) {
                $totalFotos += count($relatorio->images);
                $this->info("   📸 Fotos do relatório: " . count($relatorio->images));
            }
            
            // Calcular total de fotos das atualizações
            $relatorio->load('atualizacoes');
            foreach ($relatorio->atualizacoes as $atualizacao) {
                if (is_array($atualizacao->imagens)) {
                    $totalFotos += count($atualizacao->imagens);
                    $this->info("   📸 Fotos da atualização {$atualizacao->id}: " . count($atualizacao->imagens));
                }
            }
            
            $totalHistoricos = $relatorio->atualizacoes->count();
            
            $this->info("   📊 Total de fotos: {$totalFotos}");
            $this->info("   📝 Total de atualizações: {$totalHistoricos}");
            
            // Verificar se os ícones devem aparecer
            $iconeFotos = $totalFotos > 0 ? '✅' : '❌';
            $iconeHistoricos = $totalHistoricos > 0 ? '✅' : '❌';
            
            $this->info("   {$iconeFotos} Ícone de fotos deve aparecer: " . ($totalFotos > 0 ? 'SIM' : 'NÃO'));
            $this->info("   {$iconeHistoricos} Ícone de histórico deve aparecer: " . ($totalHistoricos > 0 ? 'SIM' : 'NÃO'));
            $this->info('');
        }

        $this->info('🎯 Para verificar no frontend:');
        $this->info('   1. Acesse a listagem de relatórios');
        $this->info('   2. Verifique se os ícones aparecem nos cards');
        $this->info('   3. Ícone de câmera = fotos, Ícone de relógio = atualizações');
        $this->info('');
        $this->info('🎉 Teste concluído!');
    }
} 