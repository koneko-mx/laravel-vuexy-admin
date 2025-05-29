<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Template\Customize;

use Illuminate\Support\Facades\{Auth, Config};

final class ___VuexyLayoutConfigSynchronizer
{
    private const CONFIG_KEY = 'koneko.core.layout.vuexy';
    private const COMPONENT  = 'admin';
    private const GROUP      = 'layout';
    private const SECTION    = 'vuexy';
    private const SUBGROUP   = 'customizer';
    private const CACHE_KEY  = 'vuexy-layout-overrides';

    public static function sync(): void
    {
        $manager = cache_m()
            ->setComponent(self::COMPONENT)
            ->context(self::GROUP, self::SECTION, self::SUBGROUP)
            ->setUser(Auth::user()) // Aplica scope=user si está logueado
            ->setKeyName(self::CACHE_KEY);

        $overrides = $manager->rememberWithTTLResolution(
            fn () => static::loadMerged()
        );

        Config::set(self::CONFIG_KEY, $overrides);
    }

    public static function clear(): void
    {
        cache_m()
            ->setComponent(self::COMPONENT)
            ->context(self::GROUP, self::SECTION, self::SUBGROUP)
            ->setUser(Auth::user())
            ->setKeyName(self::CACHE_KEY)
            ->forget();
    }

    private static function loadMerged(): array
    {
        $base = config(self::CONFIG_KEY, []);

        $settings = settings()
            ->setComponent(self::COMPONENT)
            ->context(self::GROUP, self::SECTION, self::SUBGROUP)
            ->setUser(Auth::user())
            ->getSubGroup(true);

        return array_replace_recursive($base, array_map(
            fn ($val, $key) => static::castValue($key, $val),
            $settings,
            array_keys($settings)
        ));
    }

    private static function castValue(string $key, mixed $value): mixed
    {
        return match (true) {
            in_array($key, ['hasCustomizer', 'displayCustomizer', 'footerFixed', 'menuFixed', 'menuCollapsed', 'showDropdownOnHover'], true) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            $key === 'maxQuickLinks' => (int) $value,
            default => $value,
        };
    }
}
