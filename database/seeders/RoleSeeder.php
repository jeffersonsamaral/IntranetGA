<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar role de administrador
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Acesso completo ao sistema',
                'is_active' => true,
            ]
        );

        // Atribuir todas as permissões ao admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id')->toArray());

        // Criar role de gerente
        $managerRole = Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Gerente',
                'description' => 'Gerencia usuários e algumas configurações',
                'is_active' => true,
            ]
        );

        // Permissões para o gerente
        $managerPermissions = Permission::whereIn('slug', [
            'users.view', 'users.create', 'users.edit',
            'roles.view',
            'ad-groups.view',
            'policies.view',
        ])->get();
        
        $managerRole->permissions()->sync($managerPermissions->pluck('id')->toArray());

        // Criar role de usuário
        $userRole = Role::updateOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'Usuário',
                'description' => 'Acesso básico ao sistema',
                'is_active' => true,
            ]
        );
        
        // Permissões básicas para usuários comuns
        $userPermissions = Permission::whereIn('slug', [
            'users.view',
        ])->get();
        
        $userRole->permissions()->sync($userPermissions->pluck('id')->toArray());
    }
}