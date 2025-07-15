<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up()
{
    Schema::create('equipamento_tests', function (Blueprint $table) {
        $table->id();
        $table->string('tag')->unique(); // <-- Adicione esta linha
        $table->string('nome');
        $table->string('setor')->nullable();
        $table->string('status')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('equipamento_tests');
    }
};
