<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class DebugRolesController extends Controller
{
    /**
     * Exibe todas as roles (para fins de debug)
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return view('debug.roles', compact('roles'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}