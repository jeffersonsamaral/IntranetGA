<?php

namespace App\Http\Controllers;

use App\Models\AccessPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:policies.view')->only(['index', 'show']);
        $this->middleware('permission:policies.create')->only(['create', 'store']);
        $this->middleware('permission:policies.edit')->only(['edit', 'update']);
        $this->middleware('permission:policies.delete')->only(['destroy']);
    }
    
    /**
     * Exibe a lista de políticas de acesso
     */
    public function index()
    {
        $policies = AccessPolicy::paginate(15);
        return view('admin.policies.index', compact('policies'));
    }
    
    /**
     * Exibe o formulário para criar uma nova política
     */
    public function create()
    {
        return view('admin.policies.create');
    }
    
    /**
     * Armazena uma nova política
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'resource' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'conditions' => 'required|json',
            'is_active' => 'boolean',
        ]);
        
        $policy = AccessPolicy::create([
            'name' => $validated['name'],
            'resource' => $validated['resource'],
            'action' => $validated['action'],
            'conditions' => json_decode($validated['conditions'], true),
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        Log::info("Nova política de acesso criada: {$policy->name}");
        
        return redirect()
            ->route('admin.policies.show', $policy)
            ->with('success', 'Política de acesso criada com sucesso!');
    }
    
    /**
     * Exibe detalhes de uma política específica
     */
    public function show(AccessPolicy $policy)
    {
        return view('admin.policies.show', compact('policy'));
    }
    
    /**
     * Exibe o formulário para editar uma política
     */
    public function edit(AccessPolicy $policy)
    {
        return view('admin.policies.edit', compact('policy'));
    }
    
    /**
     * Atualiza uma política existente
     */
    public function update(Request $request, AccessPolicy $policy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'resource' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'conditions' => 'required|json',
            'is_active' => 'boolean',
        ]);
        
        $policy->update([
            'name' => $validated['name'],
            'resource' => $validated['resource'],
            'action' => $validated['action'],
            'conditions' => json_decode($validated['conditions'], true),
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        Log::info("Política de acesso atualizada: {$policy->name}");
        
        return redirect()
            ->route('admin.policies.show', $policy)
            ->with('success', 'Política de acesso atualizada com sucesso!');
    }
    
    /**
     * Remove uma política
     */
    public function destroy(AccessPolicy $policy)
    {
        $name = $policy->name;
        $policy->delete();
        
        Log::info("Política de acesso removida: {$name}");
        
        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Política de acesso removida com sucesso!');
    }
}