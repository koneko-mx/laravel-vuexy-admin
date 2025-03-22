<?php

namespace Koneko\VuexyAdmin\Providers;

use Koneko\VuexyAdmin\Console\Commands\CleanInitialAvatars;
use Koneko\VuexyAdmin\Helpers\VuexyHelper;
use Koneko\VuexyAdmin\Http\Middleware\AdminTemplateMiddleware;
use Illuminate\Auth\Events\{Login,Logout};
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\{URL,Event,Blade};
use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Listeners\{ClearUserCache,HandleUserLogin};

use Koneko\VuexyAdmin\Livewire\Cache\{CacheFunctions,CacheStats,SessionStats,MemcachedStats,RedisStats};
use Koneko\VuexyAdmin\Livewire\Permissions\{PermissionsIndex,PermissionOffCanvasForm};
use Koneko\VuexyAdmin\Livewire\Profile\{UpdateProfileInformationForm,UpdatePasswordForm,TwoFactorAuthenticationForm,LogoutOtherBrowser,DeleteUserForm};
use Koneko\VuexyAdmin\Livewire\Roles\{RolesIndex,RoleCards};
use Koneko\VuexyAdmin\Livewire\Users\{UsersIndex,UsersCount,UserForm,UserOffCanvasForm};

use Koneko\VuexyAdmin\Livewire\VuexyAdmin\{LogoOnLightBgSettings,LogoOnDarkBgSettings,AppDescriptionSettings,AppFaviconSettings};
use Koneko\VuexyAdmin\Livewire\VuexyAdmin\SendmailSettings;
use Koneko\VuexyAdmin\Livewire\VuexyAdmin\{VuexyInterfaceSettings};
use Koneko\VuexyAdmin\Livewire\VuexyAdmin\{GlobalSettingsIndex,GlobalSettingOffCanvasForm};
use Koneko\VuexyAdmin\Livewire\VuexyAdmin\QuickAccessWidget;

use Koneko\VuexyAdmin\Models\User;
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
            // Usuarios
            'vuexy-admin::users-index'         => UsersIndex::class,
            'vuexy-admin::users-count'         => UsersCount::class,
            'vuexy-admin::user-form'           => UserForm::class,
            'vuexy-admin::user-offcanvas-form' => UserOffCanvasForm::class,

            // Perfil del usuario
            'vuexy-admin::update-profile-information-form' => UpdateProfileInformationForm::class,
            'vuexy-admin::update-password-form'            => UpdatePasswordForm::class,
            'vuexy-admin::two-factor-authentication'       => TwoFactorAuthenticationForm::class,
            'vuexy-admin::logout-other-browser'            => LogoutOtherBrowser::class,
            'vuexy-admin::delete-user-form'                => DeleteUserForm::class,

            // Roles y Permisos
            'vuexy-admin::roles-index'               => RolesIndex::class,
            'vuexy-admin::role-cards'                => RoleCards::class,
            'vuexy-admin::permissions-index'         => PermissionsIndex::class,
            'vuexy-admin::permission-offcanvas-form' => PermissionOffCanvasForm::class,

            // Identidad de aplicación
            'vuexy-admin::app-description-settings'  => AppDescriptionSettings::class,
            'vuexy-admin::app-favicon-settings'      => AppFaviconSettings::class,
            'vuexy-admin::logo-on-light-bg-settings' => LogoOnLightBgSettings::class,
            'vuexy-admin::logo-on-dark-bg-settings'  => LogoOnDarkBgSettings::class,

            // Ajustes de interfaz
            'vuexy-admin::interface-settings' => VuexyInterfaceSettings::class,

            // Cache
            'vuexy-admin::cache-stats'     => CacheStats::class,
            'vuexy-admin::session-stats'   => SessionStats::class,
            'vuexy-admin::redis-stats'     => RedisStats::class,
            'vuexy-admin::memcached-stats' => MemcachedStats::class,
            'vuexy-admin::cache-functions' => CacheFunctions::class,

            // Configuración de correo saliente
            'vuexy-admin::sendmail-settings' => SendmailSettings::class,

            // Configuraciones globales
            'vuexy-admin::global-settings-index' => GlobalSettingsIndex::class,
            'vuexy-admin::global-setting-offcanvas-form' => GlobalSettingOffCanvasForm::class,

            // Accesos rápidos de la barra de menú
            'vuexy-admin::quick-access-widget' => QuickAccessWidget::class,
        ];

        foreach ($components as $alias => $component) {
            Livewire::component($alias, $component);
        }


        // Registrar auditoría en usuarios
        User::observe(AuditableObserver::class);
    }
}
