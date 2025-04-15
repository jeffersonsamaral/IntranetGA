<?php

namespace App\Providers;

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Services\AccessPolicyService;
use App\Services\AdGroupSyncService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AccessControlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar os serviços no container
        $this->app->singleton(AccessPolicyService::class, function ($app) {
            return new AccessPolicyService();
        });

        $this->app->singleton(AdGroupSyncService::class, function ($app) {
            return new AdGroupSyncService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Definir os Gates para permissões
        Gate::before(function ($user, $ability) {
            // Admin tem todas as permissões
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Configurar o Gate para permissões
        Gate::define('permission', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        // Configurar o Gate para roles
        Gate::define('role', function ($user, $role) {
            return $user->hasRole($role);
        });

        // Configurar o Gate para políticas ABAC
        Gate::define('access', function ($user, $resource, $action = null) {
            $policyService = app(AccessPolicyService::class);
            return $policyService->evaluate($user, $resource, $action ?? 'view');
        });
    }
}