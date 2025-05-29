<?php

namespace Koneko\VuexyAdmin\Application\Config\Registry;

final class ConfigBlockRegistry
{
    protected static array $blocks = [];

    public static function register(string $key, array $config): void
    {
        static::$blocks[$key] = $config;
    }

    public static function get(string $key): array
    {
        if (!static::exists($key)) {
            throw new \InvalidArgumentException("Bloque '$key' no registrado.");
        }

        return static::$blocks[$key];
    }

    public static function exists(string $key): bool
    {
        return isset(static::$blocks[$key]);
    }

    public static function all(): array
    {
        return static::$blocks;
    }

    public static function clear(): void
    {
        static::$blocks = [];
    }
}
