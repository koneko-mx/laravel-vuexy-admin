<?php

namespace Koneko\VuexyAdmin\Support\Traits\Cache;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Cache, Config};
use DateTimeInterface;

/**
 * Trait para manejo estandarizado de cache en servicios Vuexy/Koneko.
 * Aplica validaciones condicionales centralizadas y TTL dinámico.
 */
trait InteractsWithKonekoVarsCache
{
    protected bool $cacheEnabled;
    protected int $cacheTTL;
    protected bool $cacheIsMenuRelated = false;

    /**
     * Inicializa las configuraciones de caché desde config/koneko.php.
     */
    protected function initCacheConfig(bool $isMenuRelated = false): void
    {
        $this->cacheIsMenuRelated = $isMenuRelated;
        $this->cacheEnabled = $this->resolveCacheEnabled();
        $this->cacheTTL     = (int) Config::get(
            $isMenuRelated ? 'koneko.admin.menu.cache.ttl' : 'koneko.admin.cache.ttl',
            1440
        );
    }

    /**
     * Verifica si la cache está activada globalmente (y opcionalmente por tipo de menú).
     */
    protected function resolveCacheEnabled(): bool
    {
        return Config::get('koneko.admin.cache.enabled', true)
            && (!$this->cacheIsMenuRelated || Config::get('koneko.admin.menu.cache.enabled', true));
    }

    /**
     * Devuelve la instancia DateTimeInterface para TTL.
     */
    protected function getCacheTtl(): DateTimeInterface
    {
        return now()->addMinutes($this->cacheTTL);
    }

    /**
     * Cachea o computa un valor si el caché está activado.
     */
    protected function cacheOrCompute(string $key, callable $callback): mixed
    {
        if (!$this->cacheEnabled) {
            return $callback();
        }

        return Cache::remember($key, $this->getCacheTtl(), $callback);
    }

    protected function cacheOrComputeForUser(callable $callback): mixed
    {
        if (!defined('static::CACHE_PREFIX')) {
            throw new \RuntimeException('Debe definir CACHE_PREFIX en la clase que usa este trait.');
        }

        if (!property_exists($this, 'user') || !($this->user instanceof Authenticatable)) {
            throw new \RuntimeException('La clase debe tener una propiedad $user de tipo Authenticatable');
        }

        $key = static::makeCacheKeyForUser($this->user->getAuthIdentifier());

        return $this->cacheOrCompute($key, $callback);
    }

    protected function cacheOrComputeTagged(string $key, callable $callback): mixed
    {
        if (!$this->cacheEnabled) {
            return $callback();
        }

        if (defined('static::CACHE_TAG')) {
            return Cache::tags([static::CACHE_TAG])->remember($key, $this->getCacheTtl(), $callback);
        }

        return Cache::remember($key, $this->getCacheTtl(), $callback);
    }

    /**
     * Elimina el valor de caché especificado.
     */
    protected function forgetCache(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Genera una clave de caché para un usuario específico.
     * Requiere que la clase que usa este trait defina una constante CACHE_PREFIX.
     */
    protected static function makeCacheKeyForUser(int|string $userId): string
    {
        return static::CACHE_PREFIX . $userId;
    }

    /**
     * Limpia el caché para un usuario específico.
     */
    public static function clearCacheForUser(int|string $userId): void
    {
        Cache::forget(static::makeCacheKeyForUser($userId));
    }

    protected static function flushCacheTags(string|array $tags): void
    {
        Cache::tags((array) $tags)->flush();
    }
}
