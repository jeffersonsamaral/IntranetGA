<?php

namespace App\Providers;

use App\Services\AdGroupSyncService;
use Illuminate\Support\ServiceProvider;
use LdapRecord\Laravel\Auth\LdapAuthenticator;
use LdapRecord\Laravel\Events\Import\Imported;
use LdapRecord\Laravel\Events\Auth\Authenticated;
use Illuminate\Support\Facades\Event;

class LdapAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Quando um usuário é importado do LDAP para o banco de dados local
        Event::listen(Imported::class, function (Imported $event) {
            $user = $event->eloquent;
            $ldapUser = $event->object;
            
            // Se for um usuário, sincronizar os grupos
            if (get_class($user) === \App\Models\User::class) {
                try {
                    app(AdGroupSyncService::class)->syncUserGroups($user, $ldapUser);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erro ao sincronizar grupos do usuário importado: ' . $e->getMessage());
                }
            }
        });
        
        // Quando um usuário é autenticado via LDAP
        Event::listen(Authenticated::class, function (Authenticated $event) {
            $user = $event->model;
            $ldapUser = $event->user;
            
            // Sincronizar os grupos a cada autenticação
            try {
                app(AdGroupSyncService::class)->syncUserGroups($user, $ldapUser);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erro ao sincronizar grupos do usuário autenticado: ' . $e->getMessage());
            }
        });
    }
}