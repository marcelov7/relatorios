<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            if (Schema::hasColumn('equipamentos', 'local_id')) {
                $table->dropForeign(['local_id']);
                $table->dropColumn('local_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('local_id')->nullable();
            // $table->foreign('local_id')->references('id')->on('locais')->onDelete('set null');
        });
    }
}; 