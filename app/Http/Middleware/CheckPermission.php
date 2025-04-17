<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Administrador tem todas as permissões
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        
        // Verifica se o usuário tem a permissão específica
        if (!$user->hasPermission($permission)) {
            abort(403, 'Acesso não autorizado para esta funcionalidade.');
        }

        return $next($request);
    }
}

    /**
     * Verificação de permissão mais detalhada
     */
    protected function checkUserPermission($user, $permission)
    {
        // Admin tem todas as permissões
        if ($user->hasRole('admin')) {
            return true;
        }
    
        // Verifica se o usuário tem a permissão específica
        return $user->hasPermission($permission);
    }

    public function permissions()
    {
        $permissionIds = [];
        
        foreach ($this->roles as $role) {
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            $permissionIds = array_merge($permissionIds, $rolePermissions);
        }
        
        return Permission::whereIn('id', array_unique($permissionIds));
    }
}