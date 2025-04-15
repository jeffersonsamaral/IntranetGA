<?php

namespace App\Http\Controllers;

use App\Models\AdGroup;
use App\Models\Role;
use App\Services\AdGroupSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdGroupMappingController extends Controller
{
    protected $adGroupSyncService;
    
    public function __construct()
    {
        $this->adGroupSyncService = app(AdGroupSyncService::class);
        // Aplicação dos middlewares diretamente no controller
        $this->middleware('auth');
    }
    
    /**
     * Exibe a lista de grupos AD com seus mapeamentos
     */
    public function index()
    {
        $adGroups = AdGroup::with('roles')->paginate(15);
        return view('admin.ad-groups.index', compact('adGroups'));
    }
    
    /**
     * Exibe detalhes de um grupo AD específico
     */
    public function show(AdGroup $adGroup)
    {
        $adGroup->load('roles');
        return view('admin.ad-groups.show', compact('adGroup'));
    }
    
    /**
     * Exibe o formulário para editar o mapeamento de um grupo AD
     */
    public function edit(AdGroup $adGroup)
    {
        $adGroup->load('roles');
        $roles = Role::where('is_active', true)->get();
        return view('admin.ad-groups.edit', compact('adGroup', 'roles'));
    }
    
    /**
     * Atualiza o mapeamento entre grupo AD e roles
     */
    public function update(Request $request, AdGroup $adGroup)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);
        
        $roleIds = $validated['roles'] ?? [];
        
        $adGroup->roles()->sync($roleIds);
        
        Log::info("Mapeamento atualizado para o grupo AD: {$adGroup->name}");
        
        return redirect()
            ->route('admin.ad-groups.show', $adGroup)
            ->with('success', 'Mapeamento atualizado com sucesso!');
    }
    
    /**
     * Inicia a sincronização de grupos AD
     */
    public function sync()
    {
        try {
            $stats = $this->adGroupSyncService->syncGroups();
            
            return redirect()
                ->route('admin.ad-groups.index')
                ->with('success', "Sincronização concluída! Total: {$stats['total']}, Criados: {$stats['created']}, Atualizados: {$stats['updated']}");
                
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar grupos AD: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.ad-groups.index')
                ->with('error', 'Erro ao sincronizar grupos AD: ' . $e->getMessage());
        }
    }
}