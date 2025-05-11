<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

trait HasEnumMacros
{
    protected static array $macros = [];

    public static function macro(string $name, callable $callback): void
    {
        static::$macros[static::class][$name] = $callback;
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[static::class][$name]);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if (static::hasMacro($name)) {
            return call_user_func_array(static::$macros[static::class][$name], $arguments);
        }

        throw new \BadMethodCallException("Macro [{$name}] not defined for enum " . static::class);
    }

    public function __call(string $name, array $arguments)
    {
        if (static::hasMacro($name)) {
            return call_user_func_array(static::$macros[static::class][$name], array_merge([$this], $arguments));
        }

        throw new \BadMethodCallException("Instance macro [{$name}] not defined for enum " . static::class);
    }
}
