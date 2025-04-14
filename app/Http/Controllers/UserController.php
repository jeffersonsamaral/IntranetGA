<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Construtor do controlador
     */
    public function __construct()
    {
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    /**
     * Exibe a lista de usuários
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Exibe informações detalhadas de um usuário
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Exibe o formulário para editar um usuário
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Atualiza as informações de um usuário
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        // Atualizar informações básicas do usuário
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email']
        ]);

        // Sincronizar roles se fornecidas
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        Log::info("Usuário atualizado: {$user->username}");

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove uma role específica de um usuário
     */
    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);
        
        Log::info("Role {$role->slug} removida do usuário {$user->username}");
        
        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', "Role removida do usuário {$user->name} com sucesso!");
    }

    /**
     * Adiciona uma role a um usuário
     */
    public function addRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);
        
        $role = Role::find($validated['role_id']);
        
        // Verificar se o usuário já tem a role
        if (!$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
            
            Log::info("Role {$role->slug} adicionada ao usuário {$user->username}");
            
            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', "Role {$role->name} adicionada com sucesso!");
        }
        
        return redirect()
            ->route('admin.users.show', $user)
            ->with('info', "O usuário já possui esta role.");
    }
}