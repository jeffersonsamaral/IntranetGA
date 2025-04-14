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

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Rotas para administração
    Route::prefix('admin')->name('admin.')->group(function () {
        // Gerenciamento de Grupos AD
        Route::get('/ad-groups/sync', [AdGroupMappingController::class, 'sync'])
            ->middleware(CheckPermission::class . ':ad-groups.sync')
            ->name('ad-groups.sync');
            
        Route::resource('ad-groups', AdGroupMappingController::class)
            ->except(['create', 'store', 'destroy']);
        
        // Gerenciamento de Roles com middlewares aplicados diretamente nas rotas
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
        
        // Gerenciamento de Permissões com middlewares aplicados diretamente nas rotas
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
        
        Route::delete('/admin/users/{user}/roles/{role}', [UserController::class, 'removeRole'])
            ->middleware('permission:roles.edit')
            ->name('admin.users.remove-role');
        
        // Gerenciamento de Políticas de Acesso
        Route::resource('policies', AccessPolicyController::class);
    });
    
    // Gerenciamento de Usuários (protegido por permissão)
    Route::resource('users', UserController::class)
        ->middleware(CheckPermission::class . ':users.view');

// Rota para garantir que o usuário tenha todas as permissões
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



// Rotas para gerenciamento de usuários
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Rotas de recursos padrão
    Route::resource('users', UserController::class);
    
    // Rota para remover uma role de um usuário
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])
        ->middleware('permission:roles.edit')
        ->name('users.remove-role');
    
    // Rota para adicionar uma role a um usuário
    Route::post('/users/{user}/roles', [UserController::class, 'addRole'])
        ->middleware('permission:users.edit')
        ->name('users.add-role');

    // Rota para adicionar usuários a uma role
    Route::post('/admin/roles/{role}/users', [RoleController::class, 'addUsers'])
    ->middleware('permission:roles.edit')
    ->name('admin.roles.add-users');

});

});