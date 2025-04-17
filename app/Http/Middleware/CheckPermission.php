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