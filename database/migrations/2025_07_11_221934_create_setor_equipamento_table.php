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
        Schema::create('setor_equipamento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setor_id');
            $table->unsignedBigInteger('equipamento_id');
            $table->timestamps();

            $table->foreign('setor_id')->references('id')->on('setores')->onDelete('cascade');
            $table->foreign('equipamento_id')->references('id')->on('equipamentos')->onDelete('cascade');
            $table->unique(['setor_id', 'equipamento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setor_equipamento');
    }
};
