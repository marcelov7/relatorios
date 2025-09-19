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
        Schema::table('inspecao_geradores', function (Blueprint $table) {
            $table->string('setor_text')->nullable()->after('setor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspecao_geradores', function (Blueprint $table) {
            $table->dropColumn('setor_text');
        });
    }
};
