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
        Schema::table('users', function (Blueprint $table) {
            $table->string('setor')->nullable()->after('email');
            $table->string('cargo')->nullable()->after('setor');
            $table->string('telefone')->nullable()->after('cargo');
            $table->enum('role', ['user', 'admin'])->default('user')->after('telefone');
            $table->boolean('ativo')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['setor', 'cargo', 'telefone', 'role', 'ativo']);
        });
    }
}; 