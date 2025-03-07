<?php

namespace Koneko\VuexyAdmin\Providers;

use Koneko\VuexyAdmin\Http\Middleware\AdminTemplateMiddleware;
use Koneko\VuexyAdmin\Listeners\{ClearUserCache,HandleUserLogin};
use Koneko\VuexyAdmin\Livewire\Users\{UserIndex,UserShow,UserForm,UserOffCanvasForm};
use Koneko\VuexyAdmin\Livewire\Roles\RoleIndex;
use Koneko\VuexyAdmin\Livewire\Permissions\PermissionIndex;
use Koneko\VuexyAdmin\Livewire\Cache\{CacheFunctions,CacheStats,SessionStats,MemcachedStats,RedisStats};
use Koneko\VuexyAdmin\Livewire\AdminSettings\{ApplicationSettings,GeneralSettings,InterfaceSettings,MailSmtpSettings,MailSenderResponseSettings};
use Koneko\VuexyAdmin\Console\Commands\CleanInitialAvatars;
use Koneko\VuexyAdmin\Helpers\VuexyHelper;
use Koneko\VuexyAdmin\Models\User;
use Illuminate\Support\Facades\{URL,Event,Blade};
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Auth\Events\{Login,Logout};
use Livewire\Livewire;
use OwenIt\Auditing\AuditableObserver;
use Spatie\Permission\PermissionServiceProvider;

class VuexyAdminServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Cargar configuraciones personalizadas
        $this->mergeConfigFrom(__DIR__.'/../config/koneko.php', 'koneko');

        // Register the module's services and providers
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(PermissionServiceProvider::class);

        // Register the module's aliases
        AliasLoader::getInstance()->alias('Helper', VuexyHelper::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(env('FORCE_HTTPS', false)){
            URL::forceScheme('https');
        }

        // Registrar alias del middleware
        $this->app['router']->aliasMiddleware('admin', AdminTemplateMiddleware::class);

        // Sobrescribir ruta de traducciones para asegurar que se usen las del paquete
        $this->app->bind('path.lang', function () {
            return __DIR__ . '/../resources/lang';
        });

        // Register the module's routes
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');


        // Cargar vistas del paquete
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'vuexy-admin');

        // Registrar Componentes Blade
        Blade::componentNamespace('VuexyAdmin\\View\\Components', 'vuexy-admin');


        // Register the migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');


        // Publicar los archivos necesarios
        $this->publishes([
            __DIR__.'/../config/fortify.php' => config_path('fortify.php'),
            __DIR__.'/../config/image.php' => config_path('image.php'),
            __DIR__.'/../config/vuexy_menu.php' => config_path('vuexy_menu.php'),
        ], 'vuexy-admin-config');

        $this->publishes([
            __DIR__.'/../database/seeders/' => database_path('seeders'),
            __DIR__.'/../database/data' => database_path('data'),
        ], 'vuexy-admin-seeders');

        $this->publishes([
            __DIR__.'/../resources/img' => public_path('vendor/vuexy-admin/img'),
        ], 'vuexy-admin-images');


        // Registrar eventos
        Event::listen(Login::class, HandleUserLogin::class);
        Event::listen(Logout::class, ClearUserCache::class);


        // Registrar comandos de consola
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanInitialAvatars::class,
            ]);
        }

        // Registrar Livewire Components
        $components = [
            'user-index' => UserIndex::class,
            'user-show'  => UserShow::class,
            'user-form'  => UserForm::class,
            'user-offcanvas-form' => UserOffCanvasForm::class,
            'role-index' => RoleIndex::class,
            'permission-index' => PermissionIndex::class,


            'general-settings' => GeneralSettings::class,
            'application-settings' => ApplicationSettings::class,
            'interface-settings' => InterfaceSettings::class,
            'mail-smtp-settings' => MailSmtpSettings::class,
            'mail-sender-response-settings' => MailSenderResponseSettings::class,
            'cache-stats' => CacheStats::class,
            'session-stats' => SessionStats::class,
            'redis-stats' => RedisStats::class,
            'memcached-stats' => MemcachedStats::class,
            'cache-functions' => CacheFunctions::class,
        ];

        foreach ($components as $alias => $component) {
            Livewire::component($alias, $component);
        }

        // Registrar auditoría en usuarios
        User::observe(AuditableObserver::class);
    }
}
