<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Contracts\ApiRegistry\ExternalApiRegistryInterface;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingScope;
use Koneko\VuexyAdmin\Application\Helpers\{VuexyHelper, VuexyNotifyHelper,VuexyToastrHelper};
use Koneko\VuexyAdmin\Application\System\KonekoSettingManager;
use Koneko\VuexyAdmin\Models\ExternalApi;
use Koneko\VuexyAdmin\Models\UserInteraction;

// =================== HELPERS ===================

if (!function_exists('Helper')) {
    function Helper(): mixed
    {
        return app(VuexyHelper::class)->appClasses();
    }
}

// =================== SETTINGS ===================
if (!function_exists('settings')) {
    function settings(
        ?string $component = null,
        ?string $group = null,
        ?string $subGroup = null,
        ?string $scope = null
    ): SettingsRepositoryInterface {
        return app(KonekoSettingManager::class)
            ->setContext(
                $component,
                $group,
                $subGroup,
                $scope
            );
    }
}

// =================== CACHE ===================
if (!function_exists('cache_manager')) {
    function cache_manager(
        ?string $component = null,
        ?string $group = null,
        ?string $subGroup = null,
        ?string $scope = null
    ): KonekoCacheManager {
        return (new KonekoCacheManager(VuexyHelper::NAMESPACE))
            ->setContext(
                $component,
                $group,
                $subGroup,
                $scope
            );
    }
}

// =================== KEY VAULT ===================
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



// =================== LOGGERS ===================

if (!function_exists('log_system')) {
    function log_system(
        string|\Koneko\VuexyAdmin\Application\Enums\SystemLog\LogLevel $level,
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
        \Koneko\VuexyAdmin\Application\Enums\UserInteractions\InteractionSecurityLevel|string $security = 'normal',
        ?string $livewireComponent = null
    ): ?UserInteraction {
        return app(KonekoUserInteractionLogger::class)
            ->record($action, $context, $security, $livewireComponent);
    }
}

// =================== GEOIP ===================

if (!function_exists('external_api')) {
    function external_api(string $slug): ?ExternalApi
    {
        return app(ExternalApiRegistryInterface::class)->find($slug);
    }
}

if (!function_exists('apis_vuexy')) {
    function apis_vuexy(): ExternalApiRegistryInterface
    {
        return app(ExternalApiRegistryInterface::class);
    }
}



// =================== NOTIFICATIONS ===================

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
