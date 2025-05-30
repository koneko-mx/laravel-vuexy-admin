<?php

/**
 * Controlador de Usuarios del ERP Koneko Vuexy Admin
 *
 * @package   Koneko\VuexyAdmin
 * @author    Arturo Corro Pacheco <opensource@koneko.mx>
 * @copyright 2025 Koneko Soluciones Tecnológicas
 * @license   Business Source License 1.1 (custom) - See LICENSE or https://github.com/koneko-mx/laravel-vuexy-admin/blob/main/LICENSE
 */

 declare(strict_types=1);

use Illuminate\Auth\Events\{Failed, Login, Logout};
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Config\Cast\VuexyLayoutCast;
use Koneko\VuexyAdmin\Application\Contracts\Loggers\{SecurityLoggerInterface, SystemLoggerInterface, UserInteractionLoggerInterface};
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Events\Settings\VuexyCustomizerSettingsUpdated;
use Koneko\VuexyAdmin\Application\Helpers\{VuexyHelper, VuexyNotifyHelper, VuexyToastrHelper};
use Koneko\VuexyAdmin\Application\Http\Middleware\{AdminTemplateMiddleware, LocaleMiddleware, TrackSessionActivity};
use Koneko\VuexyAdmin\Application\Jobs\Security\RotateVaultKeysJob;
use Koneko\VuexyAdmin\Application\Jobs\Users\ForceLogoutInactiveUsersJob;
use Koneko\VuexyAdmin\Application\Listeners\Authentication\{HandleFailedLogin, HandleUserLogin, HandleUserLogout};
use Koneko\VuexyAdmin\Application\Listeners\Settings\ApplyVuexyCustomizerSettings;
use Koneko\VuexyAdmin\Application\Loggers\{KonekoSecurityAuditLogger, KonekoUserInteractionLogger, KonekoSecurityLogger, KonekoSystemLogger};
use Koneko\VuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\LaravelLogs\LaravelLogsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\SecurityEvents\SecurityEventsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\UsersAuthLogs\UsersAuthLogsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\ModuleManagement\ModuleManagementIndex;
use Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\Plugins\{PluginsIndex, VuexyQuicklinks};
use Koneko\VuexyAdmin\Application\UI\Livewire\Pages\Dashboards\MenuAccessCards;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\EnvironmentVars\{EnvironmentVarsTable, EnvironmentVarsOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Permissions\{PermissionsTable, PermissionOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Roles\{RolesIndex, RoleCards};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Smtp\SmtpSettingsCard;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Users\{UsersTable, UsersCount, UserForm, UserOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\VuexyInterface\VuexyInterfaceIndex;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface\{LogoOnLightBgCard, LogoOnDarkBgCard, AppDescriptionCard, AppFaviconCard};
use Koneko\VuexyAdmin\Application\UI\Livewire\Tools\Cache\{CacheFunctionsCard, CacheStatsCard, SessionStatsCard, MemcachedStatsCard, RedisStatsCard};
use Koneko\VuexyAdmin\Application\UI\Livewire\User\Profile\{UpdateProfileInformationForm, UpdatePasswordForm, TwoFactorAuthenticationForm, LogoutOtherBrowser, DeleteUserForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\User\Viewer\UserDetailsViewerIndex;
use Koneko\VuexyAdmin\Console\Commands\Geolocationg\DownloadGeoIpDatabase;
use Koneko\VuexyAdmin\Console\Commands\Layout\VuexyMenuBuildCommand;
use Koneko\VuexyAdmin\Console\Commands\Layout\VuexyMenuListModulesCommand;
use Koneko\VuexyAdmin\Console\Commands\Notifications\VuexyDeviceTokenPruneCommand;
use Koneko\VuexyAdmin\Console\Commands\Orquestator\VuexySeedCommand;
use Koneko\VuexyAdmin\Console\Commands\RBAC\VuexyRbacCommand;
use Koneko\VuexyAdmin\Console\Commands\UI\VuexyAvatarInitialsCommand;
use Koneko\VuexyAdmin\Models\{Setting, User};
use Koneko\VuexyAdmin\Providers\FortifyServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

return [
    // 🌐 Identidad del Módulo
    'name' => 'Vuexy Admin',
    'description' => 'Laravel Vuexy Admin, núcleo del ERP optimizado para México.',
    'type' => 'core',
    'tags' => ['koneko-official', 'core', 'admin', 'rbac', 'erp'],

    // ⚙️ Namespace de configuraciones Koneko Vuexy Admin
    'componentNamespace' => 'core',

    // 🗒 Metadatos visuales para UI del gestor
    'ui' => [
        'image'  => 'resources/img/module-cover.png',
        'readme' => 'README.md',
    ],

    // ⚙️ Archivos de configuración del módulo
    'configs' => [
        'auth'    => 'config/auth.php',
        'fortify' => 'config/fortify.php',
        'image'   => 'config/image.php',
        'koneko'                => 'config/koneko.php',
        'koneko.core.layout'    => 'config/koneko_layout.php',
        'koneko.core.ui'        => 'config/koneko_ui.php',
        'koneko.core.logging'   => 'config/koneko_logging.php',
        'koneko.core.security'  => 'config/koneko_security.php',
        'koneko.core.key_vault' => 'config/koneko_key_vault.php',
        'database.connections.vault'  => 'config/koneko_key_vault_db.php',
    ],
    // 📦 Configuraciones de bloques
    'configBlocks' => [
        'koneko.core.layout.vuexy' => [
            'component' => 'core',
            'group'     => 'layout',
            'section'   => 'vuexy',
            'sub_group' => 'customizer',
            'key_name'  => 'vuexy-layout',
            'cast'      => VuexyLayoutCast::class,
        ],
    ],

    // 🏭 Proveedores de servicio, Middleware y Aliases (runtime)
    'providers' => [
        FortifyServiceProvider::class,
        PermissionServiceProvider::class,
    ],
    'middleware' => [
        'admin'           => AdminTemplateMiddleware::class,
        'sessionActivity' => TrackSessionActivity::class,
        'lang'            => LocaleMiddleware::class,
    ],
    'aliases' => [
        'VuexyNotify' => VuexyNotifyHelper::class,
        'VuexyToastr' => VuexyToastrHelper::class,
        'Helper'      => VuexyHelper::class,
    ],

    // 🔩 Singletons
    'Singletons' => [
        KonekoCacheManager::class,
        KonekoSecurityAuditLogger::class,
        //KonekoAdminVarsBuilder::class,
    ],

    // 🔗 Bindings de interfaces a servicios
    'bindings' => [
        SettingsRepositoryInterface::class    => KonekoSettingManager::class,
        SystemLoggerInterface::class          => KonekoSystemLogger::class,
        SecurityLoggerInterface::class        => KonekoSecurityLogger::class,
        UserInteractionLoggerInterface::class => KonekoUserInteractionLogger::class,
    ],

    // 📜 Macros
    'macros' => [
        //
    ],

    // 🔊 Eventos
    'listeners' => [
        //SettingChanged::class => SettingCacheListener::class,
        //VuexyCustomizerSettingsUpdated::class => ApplyVuexyCustomizerSettings::class,
        Login::class  => HandleUserLogin::class,
        Logout::class => HandleUserLogout::class,
        Failed::class => HandleFailedLogin::class,
    ],

    // 🧪 Modelos auditables
    'auditable' => [
        User::class,
        Setting::class,
    ],

    // 📦 migraciones
    'migrations' => [
        'database/migrations',
    ],

    // 🗺️ Rutas
    'routes' => [
        [
            'middleware' => ['web', 'auth', 'admin', 'lang'],
            'paths' => [
                'routes/admin.php',
                'routes/user.php',
                'routes/users-rbac.php',
                'routes/koneko.php',
            ],
        ],
        [
            'middleware' => ['web', 'auth', 'lang'],
            'paths' => [
                'routes/system.php',
            ],
        ],
        [
            'middleware' => ['web', 'lang'],
            'paths' => [
                'routes/pages.php',
                'routes/language.php',
            ],
        ]
    ],

    // 🗂️ Vistas, traducciones
    'views' => [
        'vuexy-admin' => 'resources/views',
    ],
    'translations' => [
        'lang' => 'resources/lang',
    ],

    // 🧩 Componentes Blade y Livewire
    'bladeComponents' => [
        //'vuexy-admin' => 'VuexyAdmin\\View\\Components',
    ],
    'livewire' => [
        'vuexy-admin' => [
            // Usuarios
            'users-table'         => UsersTable::class,
            'users-count'         => UsersCount::class,
            'user-form'           => UserForm::class,
            'user-offcanvas-form' => UserOffCanvasForm::class,

            // Roles y permisos
            'roles-index'               => RolesIndex::class,
            'role-cards'                => RoleCards::class,
            'permissions-table'         => PermissionsTable::class,
            'permission-offcanvas-form' => PermissionOffCanvasForm::class,

            // Interfaz Web
            'app-description-card'  => AppDescriptionCard::class,
            'app-favicon-card'      => AppFaviconCard::class,
            'logo-on-light-bg-card' => LogoOnLightBgCard::class,
            'logo-on-dark-bg-card'  => LogoOnDarkBgCard::class,

            // Interfaz Vuexy
            'vuexy-interface-index' => VuexyInterfaceIndex::class,

            // Configuraciones SMTP
            'smtp-settings-card' => SmtpSettingsCard::class,

            // Variables de entorno
            'environment-vars-table'          => EnvironmentVarsTable::class,
            'environment-vars-offcanvas-form' => EnvironmentVarsOffCanvasForm::class,

            // Cache
            'cache-stats-card'     => CacheStatsCard::class,
            'session-stats-card'   => SessionStatsCard::class,
            'redis-stats-card'     => RedisStatsCard::class,
            'memcached-stats-card' => MemcachedStatsCard::class,
            'cache-functions-card' => CacheFunctionsCard::class,

            // Koneko Vuexy
            'module-management-index' => ModuleManagementIndex::class,
            'plugins-index'           => PluginsIndex::class,

            // Auditoría
            'auth-users-logs-table' => UsersAuthLogsTable::class,
            'laravel-logs-table'    => LaravelLogsTable::class,
            'security-events-table' => SecurityEventsTable::class,

            // Perfil
            'update-profile-information-form' => UpdateProfileInformationForm::class,
            'update-password-form'            => UpdatePasswordForm::class,
            'two-factor-authentication-form'  => TwoFactorAuthenticationForm::class,
            'logout-other-browser'            => LogoutOtherBrowser::class,
            'delete-user-form'                => DeleteUserForm::class,

            // Visor de usuario
            'user-details-viewer-index' => UserDetailsViewerIndex::class,

            // Accesos rápidos a carpetas
            'menu-access-cards' => MenuAccessCards::class,

            // Navbar
            'vuexy-quicklinks' => VuexyQuicklinks::class,
        ]
    ],

    // 📁 Publicar archivos
    'publishedFiles' => [
        'config' => [
            'config/fortify.php' => config_path('fortify.php'),
            'config/image.php'   => config_path('image.php'),
        ],
        'assets' => [
            'resources/public' => public_path('vendor/vuexy-admin/'),
        ],
        'seeder-samples' => [
            'database/data/seeder_samples' => base_path('database/data/vuexy-admin/seeder_samples/'),
        ],
    ],

    // 🛠 Comandos Artisan
    'commands' => [
        DownloadGeoIpDatabase::class,
        VuexyAvatarInitialsCommand::class,
        VuexyDeviceTokenPruneCommand::class,
        VuexyMenuBuildCommand::class,
        VuexyMenuListModulesCommand::class,
        VuexyRbacCommand::class,
        VuexySeedCommand::class,
    ],

    // 📦 Scope Models
    'scopeModels' => [
        'user' => User::class,
    ],

    // Trabajos programados
    'schedules' => [
        [
            'job'    => ForceLogoutInactiveUsersJob::class,
            'method' => 'cron',
            'params' => ['*/10 * * * *'],
            'chain'  => ['withoutOverlapping'],
        ],
        [
            'job'    => RotateVaultKeysJob::class,
            'method' => 'cron',
            'params' => ['*/10 * * * *'],
            'chain'  => ['withoutOverlapping'],
        ],
    ],

    // 🛡️ Configuración de roles y permisos (RBAC)
    'rbac' => [
        'permissions_path' => 'database/rbac/permissions.json',
        'roles_path'       => 'database/rbac/roles.json',
    ],
];
