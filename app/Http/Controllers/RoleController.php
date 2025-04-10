<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // Removemos a aplicação de middleware do construtor
    // já que agora aplicamos diretamente nas rotas
    
    /**
     * Exibe a lista de roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }
    
    /**
     * Exibe o formulário para criar uma nova role
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }
    
    /**
     * Armazena uma nova role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        // Gerar slug a partir do nome
        $slug = Str::slug($validated['name']);
        $uniqueSlug = $slug;
        $counter = 1;
        
        // Garantir que o slug seja único
        while (Role::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }
        
        // Criar a role
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        // Sincronizar permissões
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }
        
        Log::info("Nova role criada: {$role->name}");
        
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Role criada com sucesso!');
    }
    
    /**
     * Exibe uma role específica
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'adGroups', 'users');
        return view('admin.roles.show', compact('role'));
    }
    
    /**
     * Exibe o formulário para editar uma role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }
    
    /**
     * Atualiza uma role existente
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        // Atualizar a role
        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        // Sincronizar permissões
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }
        
        Log::info("Role atualizada: {$role->name}");
        
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Role atualizada com sucesso!');
    }
    
    /**
     * Remove uma role
     */
    public function destroy(Role $role)
    {
        // Verificar se é uma role do sistema
        if (in_array($role->slug, ['admin', 'user'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Não é possível remover roles do sistema!');
        }
        
        $name = $role->name;
        $role->delete();
        
        Log::info("Role removida: {$name}");
        
        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role removida com sucesso!');
    }
}