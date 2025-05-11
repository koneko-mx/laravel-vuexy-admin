<?php

declare(strict_types=1);

use Illuminate\Auth\Events\{Failed, Login, Logout};
use Koneko\VuexyAdmin\Alication\Logger\KonekoSystemLogger;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Contracts\Loggers\{SecurityLoggerInterface, SystemLoggerInterface, UserInteractionLoggerInterface};
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Events\Settings\{SettingChanged, VuexyCustomizerSettingsUpdated};
use Koneko\VuexyAdmin\Application\Helpers\{VuexyHelper, VuexyNotifyHelper, VuexyToastrHelper};
use Koneko\VuexyAdmin\Application\Http\Middleware\{AdminTemplateMiddleware, LocaleMiddleware, TrackSessionActivity};
use Koneko\VuexyAdmin\Application\Jobs\Users\ForceLogoutInactiveUsersJob;
use Koneko\VuexyAdmin\Application\Listeners\Authentication\{HandleFailedLogin, HandleUserLogin, HandleUserLogout};
use Koneko\VuexyAdmin\Application\Listeners\Settings\{ApplyVuexyCustomizerSettings, SettingCacheListener};
use Koneko\VuexyAdmin\Application\Logger\{KonekoSecurityAuditLogger, KonekoUserInteractionLogger};
use Koneko\VuexyAdmin\Application\System\KonekoSettingManager;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\LaravelLogs\LaravelLogsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\SecurityEvents\SecurityEventsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\Audit\UsersAuthLogs\UsersAuthLogsTable;
use Koneko\VuexyAdmin\Application\UI\Livewire\Tools\Cache\{CacheFunctionsCard, CacheStatsCard, SessionStatsCard, MemcachedStatsCard, RedisStatsCard};
use Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\ModuleManagement\ModuleManagementIndex;
use Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\Plugins\PluginsIndex;
use Koneko\VuexyAdmin\Application\UI\Livewire\Pages\Dashboards\MenuAccessCards;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\EnvironmentVars\{EnvironmentVarsTable, EnvironmentVarsOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Permissions\{PermissionsTable, PermissionOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Roles\{RolesIndex, RoleCards};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Smtp\SmtpSettingsCard;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Users\{UsersTable, UsersCount, UserForm, UserOffCanvasForm};
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\VuexyInterface\VuexyInterfaceIndex;
use Koneko\VuexyAdmin\Application\UI\Livewire\Settings\WebInterface\{LogoOnLightBgCard, LogoOnDarkBgCard, AppDescriptionCard, AppFaviconCard};
use Koneko\VuexyAdmin\Application\UI\Livewire\User\Profile\{UpdateProfileInformationForm, UpdatePasswordForm, TwoFactorAuthenticationForm, LogoutOtherBrowser, DeleteUserForm};
use Koneko\VuexyAdmin\Application\Cache\VuexyVarsBuilderService;
use Koneko\VuexyAdmin\Application\Jobs\Security\RotateVaultKeysJob;
use Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\Plugins\VuexyQuicklinks;
use Koneko\VuexyAdmin\Application\UI\Livewire\User\Viewer\UserDetailsViewerIndex;
use Koneko\VuexyAdmin\Console\Commands\{VuexyAvatarInitialsCommand, VuexyListCatalogsCommand, VuexyMenuBuildCommand, VuexyMenuListModulesCommand, VuexyRbacCommand, VuexySeedCommand};
use Koneko\VuexyAdmin\Models\{Setting, User};
use Koneko\VuexyAdmin\Providers\FortifyServiceProvider;
use Koneko\VuexyAdmin\Support\Logger\KonekoSecurityLogger;
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
        'database'     => 'config/keyvault_db.php',
        'koneko'       => 'config/koneko.php',
        'koneko.admin' => 'config/koneko_admin.php',
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
        VuexyVarsBuilderService::class,
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
        SettingChanged::class => SettingCacheListener::class,
        VuexyCustomizerSettingsUpdated::class => ApplyVuexyCustomizerSettings::class,
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
        VuexyAvatarInitialsCommand::class,
        VuexyRbacCommand::class,
        VuexySeedCommand::class,
        VuexyMenuBuildCommand::class,
        VuexyMenuListModulesCommand::class,
        VuexyListCatalogsCommand::class,
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
