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
        // Verificar e corrigir nomes das tabelas pivot
        
        // Se 'permission_role' não existir mas 'role_permission' existir, não precisa fazer nada
        if (!Schema::hasTable('permission_role') && Schema::hasTable('role_permission')) {
            // A tabela já existe com outro nome, ajustamos o modelo
        }
        // Se nenhuma existir, criamos com o nome correto
        else if (!Schema::hasTable('permission_role') && !Schema::hasTable('role_permission')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('permission_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                // Garante que não haja duplicatas
                $table->unique(['role_id', 'permission_id']);
            });
        }

        // Se 'ad_group_role' não existir mas 'role_ad_group' existir, não precisa fazer nada
        if (!Schema::hasTable('ad_group_role') && Schema::hasTable('role_ad_group')) {
            // A tabela já existe com outro nome, ajustamos o modelo
        }
        // Se nenhuma existir, criamos com o nome correto
        else if (!Schema::hasTable('ad_group_role') && !Schema::hasTable('role_ad_group')) {
            Schema::create('ad_group_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ad_group_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                // Garante que não haja duplicatas
                $table->unique(['ad_group_id', 'role_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não removemos as tabelas existentes para evitar perda de dados
    }
};