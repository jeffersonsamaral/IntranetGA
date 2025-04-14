<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissões para usuários
        $userPermissions = [
            [
                'name' => 'Listar Usuários',
                'slug' => 'users.view',
                'description' => 'Ver a lista de usuários',
            ],
            [
                'name' => 'Criar Usuários',
                'slug' => 'users.create',
                'description' => 'Criar novos usuários',
            ],
            [
                'name' => 'Editar Usuários',
                'slug' => 'users.edit',
                'description' => 'Editar informações de usuários',
            ],
            [
                'name' => 'Excluir Usuários',
                'slug' => 'users.delete',
                'description' => 'Excluir usuários',
            ],
        ];

        // Permissões para roles
        $rolePermissions = [
            [
                'name' => 'Listar Roles',
                'slug' => 'roles.view',
                'description' => 'Ver a lista de roles',
            ],
            [
                'name' => 'Criar Roles',
                'slug' => 'roles.create',
                'description' => 'Criar novas roles',
            ],
            [
                'name' => 'Editar Roles',
                'slug' => 'roles.edit',
                'description' => 'Editar roles existentes',
            ],
            [
                'name' => 'Excluir Roles',
                'slug' => 'roles.delete',
                'description' => 'Excluir roles',
            ],
        ];

        // Permissões para permissões
        $permissionPermissions = [
            [
                'name' => 'Listar Permissões',
                'slug' => 'permissions.view',
                'description' => 'Ver a lista de permissões',
            ],
            [
                'name' => 'Atribuir Permissões',
                'slug' => 'permissions.assign',
                'description' => 'Atribuir permissões a roles',
            ],
        ];

        // Permissões para grupos do AD
        $adGroupPermissions = [
            [
                'name' => 'Listar Grupos AD',
                'slug' => 'ad-groups.view',
                'description' => 'Ver a lista de grupos do Active Directory',
            ],
            [
                'name' => 'Sincronizar Grupos AD',
                'slug' => 'ad-groups.sync',
                'description' => 'Sincronizar grupos do Active Directory',
            ],
            [
                'name' => 'Mapear Grupos AD',
                'slug' => 'ad-groups.map',
                'description' => 'Mapear grupos do AD para roles',
            ],
        ];

        // Permissões para políticas de acesso
        $policyPermissions = [
            [
                'name' => 'Listar Políticas',
                'slug' => 'policies.view',
                'description' => 'Ver a lista de políticas de acesso',
            ],
            [
                'name' => 'Criar Políticas',
                'slug' => 'policies.create',
                'description' => 'Criar novas políticas de acesso',
            ],
            [
                'name' => 'Editar Políticas',
                'slug' => 'policies.edit',
                'description' => 'Editar políticas de acesso existentes',
            ],
            [
                'name' => 'Excluir Políticas',
                'slug' => 'policies.delete',
                'description' => 'Excluir políticas de acesso',
            ],
        ];

        $boardPermissions = [
            [
                'name' => 'Listar Recados',
                'slug' => 'board.view',
                'description' => 'Ver os recados do mural',
            ],
            [
                'name' => 'Criar Recados',
                'slug' => 'board.create',
                'description' => 'Criar novos recados no mural',
            ],
            [
                'name' => 'Editar Recados',
                'slug' => 'board.edit',
                'description' => 'Editar recados existentes',
            ],
            [
                'name' => 'Excluir Recados',
                'slug' => 'board.delete',
                'description' => 'Excluir recados do mural',
            ],
            [
                'name' => 'Fixar Recados',
                'slug' => 'board.pin',
                'description' => 'Fixar recados importantes no topo',
            ],
        ];



        // Juntar todas as permissões e criar no banco
        $allPermissions = array_merge(
            $userPermissions,
            $rolePermissions,
            $permissionPermissions,
            $adGroupPermissions,
            $policyPermissions,
            $boardPermissions 
        );

        foreach ($allPermissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}