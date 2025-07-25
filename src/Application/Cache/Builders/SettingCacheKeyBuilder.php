<?php

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

use Illuminate\Contracts\Auth\Authenticatable;
use Koneko\VuexyAdmin\Application\Settings\SettingDefaults;
use Koneko\VuexyAdmin\Application\CoreModule;

final class SettingCacheKeyBuilder
{
    private const MAX_KEY_LENGTH = 120;

    /**
     * Construye una clave canónica de cache para un setting.
     */
    public static function build(
        string $namespace,
        string $environment = 'local',
        ?string $scope     = null,
        int|string|null $scopeId = null,
        string $component = CoreModule::COMPONENT,
        string $group     = SettingDefaults::DEFAULT_GROUP,
        string $section   = SettingDefaults::DEFAULT_SECTION,
        string $subGroup  = SettingDefaults::DEFAULT_SUB_GROUP,
        string $keyName
    ): string {
        if (empty($keyName)) {
            throw new \InvalidArgumentException("El parámetro 'keyName' no puede estar vacío.");
        }

        $scopeSegment = $scopeId
            ? "{$scope}:{$scopeId}"
            : ($scope ?? '');

        $segments = array_filter([
            $namespace,
            $environment,
            $scopeSegment,
            $component,
            $group,
            $section,
            $subGroup,
            $keyName,
        ]);

        $baseKey = implode('.', $segments);

        return strlen($baseKey) > self::MAX_KEY_LENGTH
            ? 'h:' . hash('sha1', $baseKey)
            : $baseKey;
    }

    /**
     * Verifica si una clave generada es calificada (tiene estructura válida).
     */
    public static function isQualified(string $key): bool
    {
        return str_starts_with($key, 'h:') || substr_count($key, '.') >= 5;
    }

    /**
     * Construye una clave específica basada en un usuario autenticado.
     */
    public static function forUser(
        string $namespace,
        Authenticatable|int|null $user,
        string $environment = 'production',
        string $component   = CoreModule::COMPONENT,
        string $group       = SettingDefaults::DEFAULT_GROUP,
        string $section     = SettingDefaults::DEFAULT_SECTION,
        string $subGroup    = SettingDefaults::DEFAULT_SUB_GROUP,
        string $keyName,
    ): string {
        if (empty($keyName)) {
            throw new \InvalidArgumentException("El parámetro 'keyName' no puede estar vacío.");
        }

        $userId = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user;

        return self::build(
            namespace:  $namespace,
            environment: $environment,
            scope:      'user',
            scopeId:    $userId,
            component:  $component,
            group:      $group,
            section:    $section,
            subGroup:   $subGroup,
            keyName:    $keyName,
        );
    }

    /**
     * Genera una clave hash predecible para valores extremadamente largos.
     */
    public static function hashed(string ...$segments): string
    {
        return 'h:' . hash('sha1', implode('.', $segments));
    }
}
