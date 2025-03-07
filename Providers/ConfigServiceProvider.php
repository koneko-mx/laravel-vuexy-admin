<?php

namespace Koneko\VuexyAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Services\GlobalSettingsService;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Cargar configuración del sistema
        $this->mergeConfigFrom(__DIR__.'/../config/vuexy.php', 'vuexy');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cargar configuración del sistema
        $globalSettingsService = app(GlobalSettingsService::class);
        $globalSettingsService->loadSystemConfig();

        // Cargar configuración del sistema a través del servicio
        app(GlobalSettingsService::class)->loadSystemConfig();
    }
}
