<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model;

class ModelExtensionRegistry
{
    protected static array $modelAttributes = [];
    protected static array $configExtensions = [];

    // ========= REGISTRO DE ATRIBUTOS DINÁMICOS =========

    public static function registerModelAttributes(string $modelClass, array $attributes): void
    {
        foreach ($attributes as $key => $values) {
            self::$modelAttributes[$modelClass][$key] = array_merge(
                self::$modelAttributes[$modelClass][$key] ?? [],
                $values
            );
        }
    }

    public static function getAttributesFor(string $modelClass, string $key): array
    {
        return self::$modelAttributes[$modelClass][$key] ?? [];
    }

    // ========= REGISTRO DE EXTENSIONES DE CONFIGURADORES =========

    public static function registerConfigExtensions(string $targetClass, array $extensions): void
    {
        self::$configExtensions[$targetClass] = array_merge(
            self::$configExtensions[$targetClass] ?? [],
            $extensions
        );
    }

    public static function getConfigExtensionsFor(string $targetClass): array
    {
        return self::$configExtensions[$targetClass] ?? [];
    }

    // ========= REGISTRO DE FLAGS =========

    public static function registerModelFlags(string $modelClass, array $flags): void
    {
        foreach ($flags as $flag => $description) {
            self::$modelAttributes[$modelClass]['flags'][$flag] = $description;
        }
    }

    public static function getFlagsFor(string $modelClass): array
    {
        return self::$modelAttributes[$modelClass]['flags'] ?? [];
    }
}
