<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdGroupMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardMessageController;
use App\Http\Middleware\CheckPermission;
use App\Models\BoardMessage;

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Adicionar a rota do perfil
    Route::get('/profile', function () {
    return view('profile.index');
    })->name('profile');
    
    // Rotas administrativas unificadas
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Gerenciamento de Grupos AD
        Route::get('/ad-groups/sync', [AdGroupMappingController::class, 'sync'])
            ->middleware(CheckPermission::class . ':ad-groups.sync')
            ->name('ad-groups.sync');
                
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


        Route::resource('board', BoardMessageController::class)
            ->middleware('permission:board.view');
        
        // Gerenciamento de Usuários
        Route::resource('users', UserController::class);

        
    });
});