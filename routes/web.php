<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdGroupMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AccessPolicyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DebugRolesController;

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
            ->middleware('permission:ad-groups.sync')
            ->name('ad-groups.sync');
            
        Route::resource('ad-groups', AdGroupMappingController::class)
            ->except(['create', 'store', 'destroy']);
        
        // Gerenciamento de Roles com middlewares aplicados diretamente nas rotas
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('roles.index');
            
        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')
            ->name('roles.create');
            
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')
            ->name('roles.store');
            
        Route::get('/roles/{role}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view')
            ->name('roles.show');
            
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit')
            ->name('roles.edit');
            
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('roles.update');
            
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')
            ->name('roles.destroy');
        
        // Gerenciamento de Permissões com middlewares aplicados diretamente nas rotas
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware('permission:permissions.view')
            ->name('permissions.index');
            
        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware('permission:permissions.assign')
            ->name('permissions.create');
            
        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware('permission:permissions.assign')
            ->name('permissions.store');
            
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
            ->middleware('permission:permissions.view')
            ->name('permissions.show');
            
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware('permission:permissions.assign')
            ->name('permissions.edit');
            
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
            ->middleware('permission:permissions.assign')
            ->name('permissions.update');
        
        // Gerenciamento de Políticas de Acesso
        Route::resource('policies', AccessPolicyController::class);
    });
    
    // Gerenciamento de Usuários (protegido por permissão)
    Route::resource('users', UserController::class)
        ->middleware('permission:users.view');
});