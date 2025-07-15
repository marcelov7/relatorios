<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorio_equipamento_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->foreignId('equipamento_test_id')->constrained('equipamento_tests')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['relatorio_id', 'equipamento_test_id'], 'rel_eqp_test_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorio_equipamento_test');
    }
};