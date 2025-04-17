<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
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
        // Adiciona diretiva personalizada para verificar permissões
        Blade::directive('can', function ($expression) {
            return "<?php if (auth()->check() && (auth()->user()->hasPermission({$expression}) || auth()->user()->hasRole('admin'))): ?>";
        });
        
        Blade::directive('endcan', function () {
            return "<?php endif; ?>";
        });
    }
}