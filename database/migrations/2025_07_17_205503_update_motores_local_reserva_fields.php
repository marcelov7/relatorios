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
            // Alterar campo reserva_almox de boolean para string
            $table->string('reserva_almox')->nullable()->change();
            
            // Adicionar campo local como string
            $table->string('local')->nullable()->after('fabricante');
            
            // Remover campo local_id se existir
            if (Schema::hasColumn('motores', 'local_id')) {
                $table->dropForeign(['local_id']);
                $table->dropColumn('local_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motores', function (Blueprint $table) {
            // Reverter campo reserva_almox para boolean
            $table->boolean('reserva_almox')->default(false)->change();
            
            // Remover campo local
            $table->dropColumn('local');
            
            // Recriar campo local_id
            $table->unsignedBigInteger('local_id')->nullable()->after('fabricante');
            $table->foreign('local_id')->references('id')->on('locais')->onDelete('set null');
        });
    }
};
