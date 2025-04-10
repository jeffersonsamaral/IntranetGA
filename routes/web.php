<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdGroupMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AccessPolicyController;
use App\Http\Controllers\UserController;

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Rotas para administração (protegidas por role admin)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Gerenciamento de Grupos AD
        Route::get('/ad-groups/sync', [AdGroupMappingController::class, 'sync'])->name('ad-groups.sync');
        Route::resource('ad-groups', AdGroupMappingController::class)->except(['create', 'store', 'destroy']);
        
        // Gerenciamento de Roles
        Route::resource('roles', RoleController::class);
        
        // Gerenciamento de Permissões
        Route::resource('permissions', PermissionController::class)->except(['destroy']);
        
        // Gerenciamento de Políticas de Acesso
        Route::resource('policies', AccessPolicyController::class);
    });
    
    // Gerenciamento de Usuários (protegido por permissão)
    Route::resource('users', UserController::class)->middleware('permission:users.view');
});