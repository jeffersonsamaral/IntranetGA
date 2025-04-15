<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdGroupMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardMessageController;

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rota do perfil
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');
    
    // Rotas administrativas unificadas
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Gerenciamento de Grupos AD
        Route::get('/ad-groups/sync', [AdGroupMappingController::class, 'sync'])->name('ad-groups.sync');
        Route::resource('ad-groups', AdGroupMappingController::class);
        
        // Gerenciamento de Roles
        Route::resource('roles', RoleController::class);
        Route::post('/roles/{role}/add-users', [RoleController::class, 'addUsers'])->name('roles.add-users');
        Route::post('/roles/{role}/add-ad-groups', [RoleController::class, 'addAdGroups'])->name('roles.add-ad-groups');
        Route::delete('/roles/{role}/ad-groups/{adGroup}', [RoleController::class, 'removeAdGroup'])->name('roles.remove-ad-group');
        
        // Gerenciamento de Permissões
        Route::resource('permissions', PermissionController::class);
        
        // Mural de Recados
        Route::resource('board', BoardMessageController::class);
        Route::post('/board/{board}/toggle-pin', [BoardMessageController::class, 'togglePin'])->name('board.toggle-pin');
        
        // Gerenciamento de Usuários
        Route::resource('users', UserController::class);
        Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.remove-role');
        Route::post('/users/{user}/roles', [UserController::class, 'addRole'])->name('users.add-role');
    });
});