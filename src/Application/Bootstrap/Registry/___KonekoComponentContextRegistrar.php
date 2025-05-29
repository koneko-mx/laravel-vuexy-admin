<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap\Registry;

use Illuminate\Support\Facades\App;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Loggers\{KonekoSystemLogger, KonekoSecurityLogger, KonekoUserInteractionLogger};

class ___KonekoComponentContextRegistrar
{
    protected static ?string $currentComponent = null;
    protected static ?string $currentSlug = null;

    public static function registerComponent(string $componentNamespace, ?string $slug = null): void
    {
        self::$currentComponent = $componentNamespace;
        self::$currentSlug = $slug;

        /*
        if (App::bound(SettingsRepositoryInterface::class)) {
            App::make(SettingsRepositoryInterface::class)
                ->setNamespaceByComponent($componentNamespace);
        }


        if (class_exists(KonekoCacheManager::class)) {
            try {
                cache_m($componentNamespace)->registerDefaults();
            } catch (\Throwable $e) {
                logger()->warning("[KonekoContext] No se pudo registrar defaults para CacheManager: {$e->getMessage()}");
            }
        }

        if (class_exists(KonekoSystemLogger::class)) {
            KonekoSystemLogger::setComponent($componentNamespace, $slug);
        }

        if (class_exists(KonekoSecurityLogger::class)) {
            KonekoSecurityLogger::setComponent($componentNamespace, $slug);
        }

        if (class_exists(KonekoUserInteractionLogger::class)) {
            KonekoUserInteractionLogger::setComponent($componentNamespace, $slug);
        }
        */

        // Futuro: API Manager y Event Dispatcher
        // api_manager()->setComponent($componentNamespace);
        // event_dispatcher()->context($componentNamespace);
    }

    public static function currentComponent(): ?string
    {
        return self::$currentComponent;
    }

    public static function currentSlug(): ?string
    {
        return self::$currentSlug;
    }

    public static function hasComponent(): bool
    {
        return self::$currentComponent !== null;
    }

    public static function hasSlug(): bool
    {
        return self::$currentSlug !== null;
    }

    public static function reset(): void
    {
        self::$currentComponent = null;
        self::$currentSlug = null;
    }

    public static function configPrefix(): string
    {
        return 'koneko.' . (self::$currentComponent ?? 'core');
    }

    public static function settingsKey(string $subkey): string
    {
        return self::configPrefix() . '.settings.' . $subkey;
    }

    public static function cacheKey(string $group, string $suffix): string
    {
        return self::configPrefix() . '.' . $group . '.' . $suffix;
    }

    public static function bootAfterBindings(): void
    {
        // api_manager()->registerFromComponentContext();
        // catalog_register_contextual();
    }
}
