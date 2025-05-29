<?php

namespace Koneko\VuexyAdmin\Support\Traits\Cache;

use Illuminate\Contracts\Auth\Authenticatable;

trait HasCacheManagerHelpers
{
    /**
     * Elimina directamente una clave de caché.
     */
    protected function forgetKeyCache(
        string $component,
        string $group,
        string $subGroup,
        string $key,
        Authenticatable|int|null|false $user = false,
    ): void {
        cache_m($component, $group, $subGroup, $user)
            ->setKeyName($key)
            ->forget();
    }

    /**
     * Obtiene o genera una caché por clave y usuario
     */
    protected function rememberKeyCache(
        string $component,
        string $group,
        string $subGroup,
        string $key,
        callable $callback,
        Authenticatable|int|null|false $user = false,
        ?int $ttl = null
    ): mixed {
        return cache_m($component, $group, $subGroup, $user)
            ->setKeyName($key)
            ->rememberWithTTLResolution($callback, $ttl);
    }
}
