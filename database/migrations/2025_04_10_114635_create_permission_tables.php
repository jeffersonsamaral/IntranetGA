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
        // Tabela de permissões - define ações específicas que podem ser executadas
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // Nome da permissão
            $table->string('slug')->unique(); // Identificador único (ex: create-users)
            $table->text('description')->nullable(); // Descrição da permissão
            $table->timestamps();
        });

        // Tabela de roles (funções/papéis)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // Nome da role
            $table->string('slug')->unique(); // Identificador único (ex: admin)
            $table->text('description')->nullable(); // Descrição da role
            $table->boolean('is_active')->default(true); // Status da role
            $table->timestamps();
        });

        // Tabela de grupos do Active Directory
        Schema::create('ad_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Nome do grupo no AD
            $table->string('dn')->unique();   // Distinguished Name do grupo no AD
            $table->string('sid')->unique();  // Security Identifier do AD
            $table->text('description')->nullable(); // Descrição do grupo
            $table->timestamps();
        });

        // Tabela pivot para relacionar roles e permissões (muitos para muitos)
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Garante que não haja duplicatas
            $table->unique(['role_id', 'permission_id']);
        });

        // Tabela pivot para relacionar usuários e roles (muitos para muitos)
        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Garante que não haja duplicatas
            $table->unique(['user_id', 'role_id']);
        });

        // Tabela que relaciona grupos AD com roles (muitos para muitos)
        Schema::create('ad_group_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Garante que não haja duplicatas
            $table->unique(['ad_group_id', 'role_id']);
        });

        // Tabela para atributos (ABAC)
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // Tipo de entidade (user, role, etc)
            $table->unsignedBigInteger('entity_id'); // ID da entidade
            $table->string('key');   // Chave do atributo
            $table->text('value');   // Valor do atributo
            $table->timestamps();

            // Índice para consultas rápidas
            $table->index(['entity_type', 'entity_id']);
            $table->unique(['entity_type', 'entity_id', 'key']);
        });

        // Tabela para políticas de acesso baseadas em atributos
        Schema::create('access_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Nome da política
            $table->string('resource');       // Recurso protegido
            $table->string('action');         // Ação sobre o recurso
            $table->json('conditions');       // Condições JSON com regras de atributos
            $table->boolean('is_active')->default(true); // Status da política
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_policies');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('ad_group_role');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('ad_groups');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};