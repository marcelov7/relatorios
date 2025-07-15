<?php

namespace Database\Seeders;

use App\Models\Relatorio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelatorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Relatorio::factory(25)->create();
    }
}
