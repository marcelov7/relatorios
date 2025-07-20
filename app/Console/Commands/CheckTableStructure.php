<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'check:table-structure {table}';
    protected $description = 'Verifica a estrutura de uma tabela';

    public function handle()
    {
        $table = $this->argument('table');
        
        try {
            $columns = DB::select("PRAGMA table_info({$table})");
            
            $this->info("Estrutura da tabela {$table}:");
            foreach ($columns as $column) {
                $this->line("- {$column->name} ({$column->type})");
            }
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
        }
    }
} 