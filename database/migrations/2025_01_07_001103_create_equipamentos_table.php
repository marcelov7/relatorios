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
    Schema::create('equipamentos', function (Blueprint $table) {
        $table->id();
        $table->string('equipment_tag')->unique();
        $table->string('nome');
        $table->text('descricao')->nullable();
        // Remover ou comentar a linha abaixo:
        // $table->foreignId('local_id')->constrained('locais')->onDelete('cascade');
        $table->string('tipo')->nullable();
        $table->string('marca')->nullable();
        $table->string('modelo')->nullable();
        $table->string('numero_serie')->nullable();
        $table->date('data_instalacao')->nullable();
        $table->date('data_ultima_manutencao')->nullable();
        $table->date('proxima_manutencao')->nullable();
        $table->enum('status', ['Operacional', 'Manutenção', 'Inativo', 'Defeito'])->default('Operacional');
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
        Schema::dropIfExists('equipamentos');
    }
};
