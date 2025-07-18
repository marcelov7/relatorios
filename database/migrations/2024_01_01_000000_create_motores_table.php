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
        Schema::create('motores', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique();
            $table->string('equipamento');
            $table->string('carcaca')->nullable();
            $table->decimal('potencia_kw', 8, 2)->nullable();
            $table->decimal('potencia_cv', 8, 2)->nullable();
            $table->integer('rotacao')->nullable();
            $table->decimal('corrente_nominal', 8, 2)->nullable();
            $table->decimal('corrente_configurada', 8, 2)->nullable();
            $table->enum('tipo_equipamento', [
                'Bomba', 'Ventilador', 'Compressor', 'Esteira', 'Britador',
                'Peneira', 'Moinho', 'Triturador', 'Elevador', 'Misturador',
                'Secador', 'Filtro', 'Trocador', 'Caldeira', 'Outros'
            ])->nullable();
            $table->string('fabricante')->nullable();
            $table->foreignId('local_id')->nullable()->constrained('locais')->onDelete('set null');
            $table->string('foto')->nullable();
            $table->enum('armazenamento', ['Almoxarifado', 'Instalado', 'Manutenção', 'Reserva'])->default('Almoxarifado');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motores');
    }
}; 