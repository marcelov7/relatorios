<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('motores', function (Blueprint $table) {
            // Renomear coluna tipo_equipamento para tipo_equipamento_modelo
            $table->renameColumn('tipo_equipamento', 'tipo_equipamento_modelo');
            
            // Remover coluna armazenamento (será substituída por reserva_almox)
            $table->dropColumn('armazenamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motores', function (Blueprint $table) {
            // Reverter renomeação
            $table->renameColumn('tipo_equipamento_modelo', 'tipo_equipamento');
            
            // Recriar coluna armazenamento
            $table->enum('armazenamento', ['Almoxarifado', 'Instalado', 'Manutenção', 'Reserva'])->default('Almoxarifado')->after('fabricante');
        });
    }
};
