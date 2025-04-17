<?php

namespace App\Http\Controllers;

use App\Models\AdGroup;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Exibe a lista de roles
     */
    // Exemplo para o método index
    public function index()
    {
        // Verificar permissão manualmente
        if (!auth()->user()->hasPermission('roles.view')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    // Exemplo para o método create
    public function create()
    {
        // Verificar permissão manualmente
        if (!auth()->user()->hasPermission('roles.create')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $permissions = Permission::all();
        $adGroups = AdGroup::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions', 'adGroups'));
    }

    // Exemplo para o método store
    public function store(Request $request)
    {
        // Verificar permissão manualmente
        if (!auth()->user()->hasPermission('roles.create')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'ad_groups' => 'array',
            'ad_groups.*' => 'exists:ad_groups,id'
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
        
        // Sincronizar grupos AD
        if (isset($validated['ad_groups'])) {
            $role->adGroups()->sync($validated['ad_groups']);
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
        $adGroups = AdGroup::orderBy('name')->get();
        $role->load('permissions', 'adGroups');
        return view('admin.roles.edit', compact('role', 'permissions', 'adGroups'));
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
            'permissions.*' => 'exists:permissions,id',
            'ad_groups' => 'array',
            'ad_groups.*' => 'exists:ad_groups,id'
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
        
        // Sincronizar grupos AD
        if (isset($validated['ad_groups'])) {
            $role->adGroups()->sync($validated['ad_groups']);
        } else {
            $role->adGroups()->detach();
        }
        
        Log::info("Role atualizada: {$role->name}");
        
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Regra atualizada com sucesso!');
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

    /**
     * Adiciona usuários a uma role
     */
    public function addUsers(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $user = User::find($validated['user_id']);
        
        // Verificar se o usuário já tem a role
        if (!$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
        }
        
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', "Usuário {$user->name} adicionado à role com sucesso!");
    }

            /**
         * Remove um grupo AD de uma role
         */
        public function removeAdGroup(Role $role, AdGroup $adGroup)
        {
            $role->adGroups()->detach($adGroup->id);
            
            Log::info("Grupo AD {$adGroup->name} removido da role {$role->name}");
            
            return redirect()
                ->route('admin.roles.show', $role)
                ->with('success', "Grupo AD removido da role com sucesso!");
        }

        /**
 * Adiciona grupos AD a uma role
 */
public function addAdGroups(Request $request, Role $role)
{
    $validated = $request->validate([
        'ad_group_id' => 'required|exists:ad_groups,id'
    ]);
    
    $adGroup = AdGroup::find($validated['ad_group_id']);
    
    // Verificar se a role já tem o grupo AD
    if (!$role->adGroups->contains($adGroup->id)) {
        $role->adGroups()->attach($adGroup->id);
    }
    
    return redirect()
        ->route('admin.roles.show', $role)
        ->with('success', "Grupo AD {$adGroup->name} adicionado à role com sucesso!");
}


}