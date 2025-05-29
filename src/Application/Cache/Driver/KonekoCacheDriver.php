<?php

namespace Koneko\VuexyAdmin\Application\Cache\Driver;

use Illuminate\Support\Facades\Cache;

final class KonekoCacheDriver
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    public static function put(string $key, mixed $value, int $ttl): void
    {
        Cache::put($key, $value, now()->addMinutes($ttl));
    }

    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    public static function remember(string $key, int $ttl, \Closure $callback): mixed
    {
        return Cache::remember($key, now()->addMinutes($ttl), $callback);
    }

    public static function has(string $key): bool
    {
        return Cache::has($key);
    }
}

