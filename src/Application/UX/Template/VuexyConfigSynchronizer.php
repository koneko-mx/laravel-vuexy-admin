<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Template;

use Illuminate\Support\Facades\{Cache, Config};
use Koneko\VuexyAdmin\Application\Cache\{KonekoCacheManager, VuexyVarsBuilderService};

class VuexyConfigSynchronizer
{
    private static function manager(): KonekoCacheManager
    {
        $component = VuexyVarsBuilderService::$component;
        $group     = VuexyVarsBuilderService::$group;

        return cache_manager()->setContext($component, $group);
    }

    private static function cacheKey(): string
    {
        return VuexyVarsBuilderService::SETTINGS_CUSTOMIZER_VARS_KEY;;
    }

    private static function configKey(): string
    {
        $namespace = VuexyVarsBuilderService::$namespace;

        return "{$namespace}.admin.vuexy";
    }

    public static function sync(): void
    {
        $manager = self::manager();

        $ttl = now()->addMinutes($manager->ttl());

        $settings = $manager->enabled()
            ? Cache::remember($manager->key(self::cacheKey()), $ttl, fn () => static::loadSettingsFromDb())
            : static::loadSettingsFromDb();

        Config::set(self::configKey(), $settings);
    }

    public static function clearCache(): void
    {
        $manager = self::manager();

        Cache::forget($manager->key(self::cacheKey()));
        settings()->deleteGroup(self::cacheKey());
    }

    private static function loadSettingsFromDb(): array
    {
        $base      = config(self::configKey(), []);
        $overrides = settings()->getGroup(self::cacheKey());

        foreach ($overrides as $key => &$value) {
            $value = static::castValue($key, $value);
        }

        return array_replace_recursive($base, $overrides);
    }

    private static function castValue(string $key, mixed $value): mixed
    {
        if (is_array($value)) return $value;

        return match (true) {
            in_array($key, [
                'hasCustomizer', 'displayCustomizer', 'footerFixed',
                'menuFixed', 'menuCollapsed', 'showDropdownOnHover'
            ], true) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            $key === 'maxQuickLinks' => (int) $value,
            default => $value,
        };
    }
}
