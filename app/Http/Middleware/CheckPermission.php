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
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Se não estiver autenticado, redireciona para login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Verifica se o usuário tem a permissão
        if (!$this->checkUserPermission($user, $permission)) {
            // Se não tiver permissão, aborta com erro 403
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
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
}