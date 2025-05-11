<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog;

use Koneko\VuexyAdmin\Application\Contracts\Catalogs\CatalogServiceInterface;

/**
 * Registry centralizado de servicios de catálogos.
 *
 * Permite registrar servicios por "componente" (slug) y resolverlos desde cualquier parte.
 */
class CatalogModuleRegistry
{
    /** @var array<string, class-string<CatalogServiceInterface>> */
    protected static array $registry = [];

    /**
     * Registra un servicio de catálogo para un componente.
     */
    public static function register(string $component, string $serviceClass): void
    {
        static::$registry[$component] = $serviceClass;
    }

    /**
     * Retorna una instancia del servicio de catálogo asociado al componente.
     */
    public static function get(string $component): ?CatalogServiceInterface
    {
        if (!isset(static::$registry[$component])) {
            return null;
        }

        return app(static::$registry[$component]);
    }

    /**
     * Devuelve todos los componentes registrados.
     */
    public static function all(): array
    {
        return array_keys(static::$registry);
    }

    /**
     * Verifica si un componente está registrado.
     */
    public static function has(string $component): bool
    {
        return isset(static::$registry[$component]);
    }
}
