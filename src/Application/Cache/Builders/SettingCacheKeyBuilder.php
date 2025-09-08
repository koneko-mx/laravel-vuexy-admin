<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

final class SettingCacheKeyBuilder
{
    /** Máximo permitido en Memcached (clave) */
    private const MAX_CACHE_KEY = 250;
    /** Máximo recomendado para DB (columna `key` VARCHAR) */
    private const MAX_DB_KEY = 198; // ajusta a 250 si cambias la columna en MariaDB

    /** Prefijo por defecto, configurable vía .env (CACHE_KEY_PREFIX) */
    private static function defaultPrefix(): string
    {
        return env('CACHE_KEY_PREFIX', 'st:');
    }

    /** Límite de DB configurable (por si ajustas la columna a 250) */
    private static function dbMax(): int
    {
        return (int) env('CACHE_KEY_DB_MAX', self::MAX_DB_KEY); // por defecto 198
    }

    /**
     * Construye la clave canónica legible uniendo segmentos con '.'
     * Valida ASCII y longitud para almacenarse en DB (<= MAX_DB_KEY por defecto 198).
     */
    public static function build(
        string $namespace,
        string $environment,
        ?string $scope,
        int|string|null $scopeId,
        string $component,
        string $group,
        string $section,
        string $subGroup,
        string $keyName
    ): string {
        if ($keyName === '') {
            throw new \InvalidArgumentException("El parámetro 'keyName' no puede estar vacío.");
        }

        $scopeSegment = $scopeId
            ? ($scope ? "$scope:$scopeId" : (string)$scopeId)
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
        ], static fn ($v) => $v !== null && $v !== '');

        $key = implode('.', $segments);
        self::assertAsciiKey($key);
        self::assertDbLength($key);
        return $key;
    }

    /** Prefija una clave para cache (namespacing configurable en .env). Valida límite de cache (<=250). */
    public static function withPrefix(string $key, ?string $prefix = null): string
    {
        $full = ($prefix ?? self::defaultPrefix()) . $key;
        self::assertCacheLength($full);
        return $full;
    }

    /** Estructura mínima: al menos 6 puntos => 7 segmentos */
    public static function isQualified(string $key): bool
    {
        return substr_count($key, '.') >= 6;
    }

    private static function assertAsciiKey(string $key): void
    {
        if (!preg_match('/^[\x20-\x7E]+$/', $key)) {
            throw new \InvalidArgumentException('La clave debe ser ASCII imprimible.');
        }
        if (!preg_match('/^[A-Za-z0-9._:\-]+(\.[A-Za-z0-9._:\-]+)*$/', $key)) {
            throw new \InvalidArgumentException('Segmentos inválidos. Solo [A-Za-z0-9._:-] y puntos como separador.');
        }
    }

    private static function assertDbLength(string $key): void
    {
        $max = self::dbMax(); // por defecto 198
        if (strlen($key) > $max) {
            throw new \InvalidArgumentException("La clave excede el límite de {$max} caracteres para DB.");
        }
    }

    private static function assertCacheLength(string $key): void
    {
        if (strlen($key) > self::MAX_CACHE_KEY) {
            throw new \InvalidArgumentException('La clave (con prefijo) excede el límite de 250 caracteres para cache.');
        }
    }
}
