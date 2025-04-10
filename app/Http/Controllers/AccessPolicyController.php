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
            'resource' => 'required|string|max