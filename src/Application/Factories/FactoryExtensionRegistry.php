<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Factories;

/**
 * 📚 FactoryExtensionRegistry
 *
 */
class FactoryExtensionRegistry
{
    protected static array $registeredTraits = [];

    public static function registerFactoryTrait(string $factoryClass, string $traitClass): void
    {
        static::$registeredTraits[$factoryClass][] = $traitClass;
    }

    public static function getFactoryTraits(string $factoryClass): array
    {
        return static::$registeredTraits[$factoryClass] ?? [];
    }
}
