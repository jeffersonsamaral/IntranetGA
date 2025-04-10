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
        // Verifica se a tabela users existe
        if (Schema::hasTable('users')) {
            // Verifica se a coluna remember_token não existe
            if (!Schema::hasColumn('users', 'remember_token')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->rememberToken()->after('password');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remove a coluna para evitar perda de dados
    }
};