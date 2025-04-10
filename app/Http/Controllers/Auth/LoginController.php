<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AdGroupSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $adGroupSyncService;

    public function __construct(AdGroupSyncService $adGroupSyncService)
    {
        $this->adGroupSyncService = $adGroupSyncService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember');
        
        // Log para debug
        Log::info('Tentativa de login para usuário: ' . $credentials['username']);
        Log::info('Lembrar-me está ' . ($remember ? 'ativado' : 'desativado'));
    
        // Verificar se as credenciais são válidas no LDAP
        if (Auth::attempt(['samaccountname' => $credentials['username'], 'password' => $credentials['password']])) {
            Log::info('Autenticação LDAP bem-sucedida para: ' . $credentials['username']);
            
            // Obter o usuário autenticado
            $user = Auth::user();
            
            // Sincronizar grupos e roles do AD
            try {
                // Obter o usuário LDAP
                $ldapUser = LdapUser::where('samaccountname', $credentials['username'])->first();
                
                if ($ldapUser) {
                    // Sincronizar os grupos e roles do AD
                    $roles = $this->adGroupSyncService->syncUserGroups($user, $ldapUser);
                    Log::info('Roles sincronizadas para o usuário: ' . implode(', ', $roles));
                } else {
                    Log::warning('Usuário LDAP não encontrado: ' . $credentials['username']);
                }
            } catch (\Exception $e) {
                Log::error('Erro ao sincronizar grupos do AD: ' . $e->getMessage());
                // Continuar com o login, mesmo se a sincronização falhar
            }
            
            // Se quisermos lembrar o usuário, forçamos a criação de um token de remember e o salvamos
            if ($remember) {
                // Gera um novo token de remember
                $token = Str::random(60);
                
                // Salva o token no banco de dados
                $user->setRememberToken($token);
                $user->save();
                
                // Configura o cookie de remember
                $cookie = Auth::getRecallerName();
                $cookieValue = $user->id . '|' . $token . '|' . $user->getAuthPassword();
                
                // Duração do cookie em minutos (30 dias = 43200 minutos)
                $cookieDuration = 43200;
                
                // Cria o cookie
                cookie($cookie, $cookieValue, $cookieDuration);
                
                Log::info('Remember token gerado: ' . $token);
            }
            
            // Regenerar sessão para evitar session fixation
            $request->session()->regenerate();
            
            Log::info('Login bem-sucedido para: ' . $credentials['username']);
            return redirect()->intended('dashboard');
        }
    
        Log::warning('Falha na autenticação para usuário: ' . $credentials['username']);
        return back()->withErrors([
            'username' => 'Credenciais inválidas ou usuário não encontrado no AD.'
        ])->withInput($request->only('username', 'remember'));
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}