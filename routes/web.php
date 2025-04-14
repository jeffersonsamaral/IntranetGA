<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdGroupMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AccessPolicyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DebugRolesController;
use App\Http\Middleware\CheckPermission;

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rota de debug para roles (temporária)
Route::get('/debug/roles', [DebugRolesController::class, 'index'])->middleware('auth');

// Rota para diagnóstico de permissões
Route::get('/diagnostico-usuario', function () {
    $user = auth()->user();
    $roles = $user->roles()->with('permissions')->get();
    
    $roleData = [];
    foreach ($roles as $role) {
        $roleData[$role->slug] = [
            'name' => $role->name,
            'is_active' => $role->is_active,
            'permissions' => $role->permissions->pluck('slug')->toArray()
        ];
    }
    
    return [
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
        ],
        'roles' => $roleData,
        'can_view_roles' => $user->hasPermission('roles.view'),
        'can_create_roles' => $user->hasPermission('roles.create'),
        'can_edit_roles' => $user->hasPermission('roles.edit'),
    ];
})->middleware('auth');

// Rota para atribuir permissões
Route::get('/atribuir-permissoes', function () {
    $user = auth()->user();
    
    // Verificar se a role admin existe ou criar
    $adminRole = \App\Models\Role::where('slug', 'admin')->first();
    
    if (!$adminRole) {
        $adminRole = \App\Models\Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Acesso completo ao sistema',
            'is_active' => true,
        ]);
    }
    
    // Verificar se existem permissões ou criar
    $permissionSlugs = [
        'users.view', 'users.create', 'users.edit', 'users.delete',
        'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        'permissions.view', 'permissions.assign',
        'ad-groups.view', 'ad-groups.sync', 'ad-groups.map',
        'policies.view', 'policies.create', 'policies.edit', 'policies.delete'
    ];
    
    foreach ($permissionSlugs as $slug) {
        $permission = \App\Models\Permission::where('slug', $slug)->first();
        
        if (!$permission) {
            $name = ucwords(str_replace('.', ' ', $slug));
            \App\Models\Permission::create([
                'name' => $name,
                'slug' => $slug,
                'description' => 'Permissão para ' . strtolower($name),
            ]);
        }
    }
    
    // Atribuir todas as permissões para a role admin
    $allPermissions = \App\Models\Permission::all();
    $adminRole->permissions()->sync($allPermissions->pluck('id')->toArray());
    
    // Atribuir a role admin ao usuário atual
    $user->roles()->syncWithoutDetaching([$adminRole->id]);
    
    return redirect('/dashboard')->with('success', 'Permissões atribuídas com sucesso!');
})->middleware('auth');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Rotas administrativas unificadas
    Route::prefix('admin')->name('admin.')->group(function () {
        
    // Gerenciamento de Grupos AD
    Route::get('/ad-groups/sync', [AdGroupMappingController::class, 'sync'])
    ->middleware(CheckPermission::class . ':ad-groups.sync')
    ->name('ad-groups.sync');
            
    Route::resource('ad-groups', AdGroupMappingController::class)
        ->except(['create', 'store', 'destroy']);

        Route::get('/ad-groups', [AdGroupMappingController::class, 'index'])
        ->middleware(CheckPermission::class . ':ad-groups.view')
        ->name('ad-groups.index');
    
    Route::get('/ad-groups/{adGroup}', [AdGroupMappingController::class, 'show'])
        ->middleware(CheckPermission::class . ':ad-groups.view')
        ->name('ad-groups.show');
    
    Route::get('/ad-groups/{adGroup}/edit', [AdGroupMappingController::class, 'edit'])
        ->middleware(CheckPermission::class . ':ad-groups.map')
        ->name('ad-groups.edit');
    
    Route::put('/ad-groups/{adGroup}', [AdGroupMappingController::class, 'update'])
        ->middleware(CheckPermission::class . ':ad-groups.map')
        ->name('ad-groups.update');
        
        
        // Gerenciamento de Roles
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware(CheckPermission::class . ':roles.view')
            ->name('roles.index');
            
        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware(CheckPermission::class . ':roles.create')
            ->name('roles.create');
            
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware(CheckPermission::class . ':roles.create')
            ->name('roles.store');
            
        Route::get('/roles/{role}', [RoleController::class, 'show'])
            ->middleware(CheckPermission::class . ':roles.view')
            ->name('roles.show');
            
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware(CheckPermission::class . ':roles.edit')
            ->name('roles.edit');
            
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware(CheckPermission::class . ':roles.edit')
            ->name('roles.update');
            
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':roles.delete')
            ->name('roles.destroy');

        // Adicionar usuários a uma role
        Route::post('/roles/{role}/users', [RoleController::class, 'addUsers'])
            ->middleware(CheckPermission::class . ':roles.edit')
            ->name('roles.add-users');
        
        // Gerenciamento de Permissões
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware(CheckPermission::class . ':permissions.view')
            ->name('permissions.index');
            
        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware(CheckPermission::class . ':permissions.assign')
            ->name('permissions.create');
            
        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware(CheckPermission::class . ':permissions.assign')
            ->name('permissions.store');
            
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
            ->middleware(CheckPermission::class . ':permissions.view')
            ->name('permissions.show');
            
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware(CheckPermission::class . ':permissions.assign')
            ->name('permissions.edit');
            
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
            ->middleware(CheckPermission::class . ':permissions.assign')
            ->name('permissions.update');
        
        // Gerenciamento de Políticas de Acesso
        Route::resource('policies', AccessPolicyController::class);
        
        // Gerenciamento de Usuários
        Route::resource('users', UserController::class);
        
        // Gerenciamento de relação entre usuários e roles
        Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])
            ->middleware(CheckPermission::class . ':roles.edit')
            ->name('users.remove-role');
        
        Route::post('/users/{user}/roles', [UserController::class, 'addRole'])
            ->middleware(CheckPermission::class . ':users.edit')
            ->name('users.add-role');
    });
});

// Rota para testar a conexão LDAP (temporária)
Route::get('/ldap-test', function () {
    try {
        // Tenta conectar ao servidor LDAP
        $connection = new \LdapRecord\Connection([
            'hosts' => [env('LDAP_HOST', '127.0.0.1')],
            'username' => env('LDAP_USERNAME'),
            'password' => env('LDAP_PASSWORD'),
            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN'),
            'timeout' => env('LDAP_TIMEOUT', 5),
            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
        ]);
        
        $connection->connect();
        
        // Se chegou aqui, a conexão foi bem-sucedida
        echo "<p style='color:green'>✅ Conexão LDAP estabelecida com sucesso!</p>";
        
        // Tentar buscar grupos
        try {
            $query = $connection->query();
            
            // Buscar grupos (objectClass=group é para Active Directory)
            $groups = $query->where([
                ['objectClass', '=', 'group'],
            ])->get();
            
            echo "<p>Grupos encontrados: " . count($groups) . "</p>";
            
            if (count($groups) > 0) {
                echo "<ul>";
                foreach ($groups as $group) {
                    echo "<li>{$group['name'][0]} ({$group['distinguishedname'][0]})</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color:orange'>⚠️ Nenhum grupo encontrado. Verifique as permissões e o filtro de busca.</p>";
            }
        } catch (\Exception $e) {
            echo "<p style='color:red'>❌ Erro ao buscar grupos: " . $e->getMessage() . "</p>";
        }
        
    } catch (\Exception $e) {
        echo "<p style='color:red'>❌ Erro de conexão LDAP: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr><h3>Configurações LDAP atuais:</h3>";
    echo "<pre>";
    echo "LDAP_HOST: " . env('LDAP_HOST', '127.0.0.1') . "\n";
    echo "LDAP_PORT: " . env('LDAP_PORT', 389) . "\n";
    echo "LDAP_BASE_DN: " . env('LDAP_BASE_DN', 'dc=local,dc=com') . "\n";
    echo "LDAP_USERNAME: " . (env('LDAP_USERNAME') ? '[configurado]' : '[não configurado]') . "\n";
    echo "LDAP_PASSWORD: " . (env('LDAP_PASSWORD') ? '[configurado]' : '[não configurado]') . "\n";
    echo "LDAP_SSL: " . (env('LDAP_SSL', false) ? 'true' : 'false') . "\n";
    echo "LDAP_TLS: " . (env('LDAP_TLS', false) ? 'true' : 'false') . "\n";
    echo "</pre>";
})->middleware('auth');