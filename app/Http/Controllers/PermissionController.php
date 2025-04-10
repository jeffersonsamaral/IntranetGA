<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    // Removemos a aplicação de middleware do construtor
    // já que agora aplicamos diretamente nas rotas
    
    /**
     * Exibe a lista de permissões
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }
    
    /**
     * Exibe detalhes de uma permissão específica
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }
    
    /**
     * Exibe o formulário para editar a atribuição de permissões
     */
    public function edit(Permission $permission)
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        $permission->load('roles');
        
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }
    
    /**
     * Atualiza a atribuição de permissões a roles
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);
        
        $roleIds = $validated['roles'] ?? [];
        
        // Sincronizar roles com esta permissão
        // Certificando-se de usar o nome correto da tabela pivot
        $permission->roles()->sync($roleIds);
        
        Log::info("Permissão '{$permission->name}' atualizada com " . count($roleIds) . " roles");
        
        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'Permissão atualizada com sucesso!');
    }
    
    /**
     * Exibe o formulário para criar uma nova permissão
     */
    public function create()
    {
        return view('admin.permissions.create');
    }
    
    /**
     * Armazena uma nova permissão
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string'
        ]);
        
        $permission = Permission::create($validated);
        
        Log::info("Nova permissão criada: {$permission->name}");
        
        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'Permissão criada com sucesso!');
    }
}