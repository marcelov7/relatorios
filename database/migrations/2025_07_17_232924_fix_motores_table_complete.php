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
            // Remover campos incorretos se existirem
            if (Schema::hasColumn('motores', 'carcaca')) {
                $table->dropColumn('carcaca');
            }
            
            if (Schema::hasColumn('motores', 'corrente_nominal')) {
                $table->dropColumn('corrente_nominal');
            }
            
            if (Schema::hasColumn('motores', 'local_id')) {
                try {
                    $table->dropForeign(['local_id']);
                } catch (Exception $e) {
                    // Ignora erro se foreign key não existir
                }
                $table->dropColumn('local_id');
            }
            
            // Adicionar campos corretos se não existirem
            if (!Schema::hasColumn('motores', 'carcaca_fabricante')) {
                $table->string('carcaca_fabricante')->nullable()->after('equipamento');
            }
            
            if (!Schema::hasColumn('motores', 'corrente_placa')) {
                $table->decimal('corrente_placa', 8, 2)->nullable()->after('rotacao');
            }
            
            if (!Schema::hasColumn('motores', 'reserva_almox')) {
                $table->string('reserva_almox')->nullable()->after('fabricante');
            }
            
            if (!Schema::hasColumn('motores', 'local')) {
                $table->string('local')->nullable()->after('reserva_almox');
            }
            
            if (!Schema::hasColumn('motores', 'armazenamento')) {
                $table->string('armazenamento')->nullable()->after('local');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motores', function (Blueprint $table) {
            // Reverter para estrutura incorreta (se necessário)
            if (Schema::hasColumn('motores', 'carcaca_fabricante')) {
                $table->dropColumn('carcaca_fabricante');
            }
            
            if (Schema::hasColumn('motores', 'corrente_placa')) {
                $table->dropColumn('corrente_placa');
            }
            
            if (Schema::hasColumn('motores', 'reserva_almox')) {
                $table->dropColumn('reserva_almox');
            }
            
            if (Schema::hasColumn('motores', 'local')) {
                $table->dropColumn('local');
            }
            
            if (Schema::hasColumn('motores', 'armazenamento')) {
                $table->dropColumn('armazenamento');
            }
            
            // Recriar campos incorretos
            $table->string('carcaca')->nullable()->after('equipamento');
            $table->decimal('corrente_nominal', 8, 2)->nullable()->after('rotacao');
            $table->unsignedBigInteger('local_id')->nullable()->after('fabricante');
            try {
                $table->foreign('local_id')->references('id')->on('locais')->onDelete('set null');
            } catch (Exception $e) {
                // Ignora erro se tabela locais não existir
            }
        });
    }
};
