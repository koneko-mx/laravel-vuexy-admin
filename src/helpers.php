<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;
use Koneko\VuexyAdmin\Application\Cache\Contracts\CacheRepositoryInterface;
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Config\Contracts\ConfigRepositoryInterface;
use Koneko\VuexyAdmin\Application\Config\Manager\KonekoConfigManager;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\VuexyAdmin\Application\Helpers\VuexyHelper;
use Koneko\VuexyAdmin\Application\Loggers\{KonekoSecurityLogger, KonekoSystemLogger, KonekoUserInteractionLogger};
use Koneko\VuexyAdmin\Application\UX\Notifications\Manager\KonekoNotifyManager;
use Koneko\VuexyAdmin\Models\SystemLog;
use Koneko\VuexyAdmin\Models\UserInteraction;
use Koneko\VuexyAdmin\Support\Enums\SystemLog\LogLevel;
use Koneko\VuexyAdmin\Support\Enums\UserInteractions\InteractionSecurityLevel;

// =================== HELPERS ===================

if (!function_exists('Helper')) {
    function Helper(): mixed
    {
        return app(VuexyHelper::class)->appClasses();
    }
}

// =================== CONFIG ===================

if (!function_exists('config_m')) {
    function config_m(?string $moduleComponent = null): ConfigRepositoryInterface
    {
        $manager = KonekoConfigManager::make();

        // Componente o Clase de Modulo
        if ($moduleComponent) {
            $manager->setComponent($moduleComponent);
        }

        return $manager;
    }
}


// =================== SETTINGS ===================

if (!function_exists('settings')) {
    /**
     * Devuelve una instancia de SettingsManager con contexto aplicado automáticamente.
     *
     * @param  string|array|Model|null  $context
     * - string: asume solo componente.
     * - array: se mapea a component, group, sub_group, section, key_name, etc.
     * - Model: se intenta extraer scope con `withScopeFromModel()`.
     *
     * @return SettingsRepositoryInterface
     */
    function settings(?string $moduleComponent = null): SettingsRepositoryInterface
    {
        $manager = KonekoSettingManager::make();

        // Componente o Clase de Modulo
        if ($moduleComponent) {
            $manager->setComponent($moduleComponent);
        }

        return $manager;
    }
}


// =================== CACHE ===================

if (!function_exists('cache_m')) {
    /**
     * Crea un gestor de caché con contexto aplicado.
     *
     * Ejemplos:
     * - `cache_m('site')`
     * - `cache_m(['component' => 'site', 'group' => 'seo', 'key_name' => 'enabled'])`
     * - `cache_m($empresaModel)`
     *
     * @param string|array|Model|null $context
     * @return CacheRepositoryInterface
     */
    function cache_m(?string $moduleComponent = null): CacheRepositoryInterface
    {
        $manager = KonekoCacheManager::make();

        // Componente o Clase de Modulo
        if ($moduleComponent) {
            $manager->setComponent($moduleComponent);
        }

        return $manager;
    }
}


// =================== LOGGERS ===================

if (!function_exists('log_system')) {
    function log_system(
        string|LogLevel $level,
        string $message,
        array $context = [],
        ?\Illuminate\Database\Eloquent\Model $related = null
    ): SystemLog {
        return app(KonekoSystemLogger::class)
            ->log($level, $message, $context, $related);
    }
}

if (!function_exists('log_security')) {
    function log_security(
        string $type,
        ?\Illuminate\Http\Request $request = null,
        ?int $userId = null,
        array $payload = [],
        bool $isProxy = false
    ): void {
        app(KonekoSecurityLogger::class)
            ->logEvent($type, $request, $userId, $payload, $isProxy);
    }
}

if (!function_exists('log_interaction')) {
    function log_interaction(
        string $action,
        array $context = [],
        InteractionSecurityLevel|string $security = 'normal',
        ?string $livewireComponent = null
    ): ?UserInteraction {
        return app(KonekoUserInteractionLogger::class)
            ->record($action, $context, $security, $livewireComponent);
    }
}

// =================== GEOIP ===================
/*
if (!function_exists('external_api')) {
    function external_api(string $slug): ?ExternalApi
    {
        return app(ExternalApiRegistryInterface::class)->find($slug);
    }
}

/*
if (!function_exists('apis_vuexy')) {
    function apis_vuexy(): ExternalApiRegistryInterface
    {
        return app(ExternalApiRegistryInterface::class);
    }
}
*/



// =================== NOTIFICATIONS ===================

if (!function_exists('notify')) {
    function notify(): KonekoNotifyManager
    {
        return app(KonekoNotifyManager::class);
    }
}

/*
if (!function_exists('vuexy_notify')) {
    function vuexy_notify(
        string $message,
        string $type = 'info',
        string $target = 'body',
        int $delay = 5000
    ): void {
        VuexyNotifyHelper::flash(
            $message,
            $type,
            $target,
            $delay
        );
    }
}

if (!function_exists('vuexy_toastr')) {
    function vuexy_toastr(
        string $message,
        string $type = 'info',
        int $delay = 5000
    ): void {
        VuexyToastrHelper::flash(
            $message,
            $type,
            $delay
        );
    }
}
*/

// =================== CATALOGS ===================

if (!function_exists('catalog')) {
    /**
     * Accede a un servicio de catálogos registrado por componente.
     *
     * @param string $component
     * @return \Koneko\VuexyAdmin\Application\Services\Contracts\CatalogServiceInterface|null
     */
    function catalog(string $component)
    {
        return CatalogModuleRegistry::get($component);
    }
}



// =================== KEY VAULT ===================
/*
if (!function_exists('vault_value_key')) {
    function vault_value_key(): string
    {
        static $cachedKey = null;

        if ($cachedKey) {
            return $cachedKey;
        }

        $path = env('VAULT_VALUE_KEY_PATH');

        if (!$path || !file_exists($path)) {
            throw new \RuntimeException("Vault Value Key file not found at {$path}");
        }

        $key = trim(file_get_contents($path));

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        if (empty($key)) {
            throw new \RuntimeException("Vault Value Key is invalid or empty.");
        }

        return $cachedKey = $key;
    }
}
*/
