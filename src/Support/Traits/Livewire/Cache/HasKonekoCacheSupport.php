<?php

namespace Koneko\VuexyAdmin\Support\Traits\Livewire\Cache;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;

/**
 * Trait que otorga acceso directo al CacheManager desde un componente Livewire.
 * Compatible con cache por usuario o global.
 */
trait HasKonekoCacheSupport
{
    protected ?Authenticatable $cacheUserContext = null;
    protected string $cacheComponentKey = 'admin';
    protected string $cacheGroupKey     = 'cache';
    protected ?KonekoCacheManager $cacheManager = null;

    protected function cacheInit(?string $component = null, ?string $group = null, ?Authenticatable $user = null): void
    {
        $this->cacheComponentKey = $component ?? $this->cacheComponentKey;
        $this->cacheGroupKey     = $group ?? $this->cacheGroupKey;
        $this->cacheUserContext  = $user ?? Auth::user();
        $this->cacheManager      = new KonekoCacheManager($this->cacheComponentKey, $this->cacheGroupKey);
    }

    protected function cacheKey(string $suffix): string
    {
        return $this->cacheManager->key($suffix);
    }

    protected function cacheRemember(string $keySuffix, \Closure $callback): mixed
    {
        if (! $this->cacheManager->enabled()) {
            return $callback();
        }

        return cache()->remember(
            $this->cacheKey($keySuffix),
            now()->addMinutes($this->cacheManager->ttl()),
            $callback
        );
    }

    protected function cacheRememberPerUser(string $keySuffix, \Closure $callback): mixed
    {
        $id = $this->cacheUserContext?->getAuthIdentifier() ?? 'guest';
        return $this->cacheRemember("{$id}:{$keySuffix}", $callback);
    }

    protected function cacheForget(string $keySuffix): void
    {
        cache()->forget($this->cacheKey($keySuffix));
    }

    protected function cacheTtl(): int
    {
        return $this->cacheManager->ttl();
    }

    protected function cacheInfo(): array
    {
        return $this->cacheManager->info();
    }
}
