<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Cache;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use DateTimeInterface;
use RuntimeException;

/**
 * Trait universal de soporte de caché para el ecosistema Koneko.
 * Proporciona una capa de cacheo compatible sin tags.
 */
trait KonekoCacheSupport
{
    protected string $cacheNamespace = 'global';
    protected string $cacheGroup = 'default';
    protected bool $cacheEnabled = true;
    protected int $cacheTTL = 1440; // minutos

    /**
     * Inicializa configuración de caché de forma estándar.
     *
     * @param string $configKey Ruta de config (ej: koneko.admin.menu.cache)
     * @param int $defaultTTL Minutos de TTL por defecto
     */
    /**
     * Inicializa configuración de caché de forma estándar.
     *
     * @param string $configKey Ruta de config (ej: koneko.admin.menu.cache)
     * @param int $defaultTTL Minutos de TTL por defecto
     */
    protected function initKonekoCache(string $configKey = 'koneko.admin.cache', int $defaultTTL = 1440): void
    {
        $this->konekoCacheEnabled = Config::get("{$configKey}.enabled", true);
        $this->konekoCacheTTL     = (int) Config::get("{$configKey}.ttl", $defaultTTL);
    }

    /**
     * Genera TTL compatible con Cache::remember
     */
    protected function getKonekoCacheTTL(): DateTimeInterface
    {
        return now()->addMinutes($this->konekoCacheTTL);
    }

    /**
     * Recuerda el valor cacheado o ejecuta el callback
     */
    protected function remember(string $key, Closure $callback): mixed
    {
        if (!$this->konekoCacheEnabled) {
            return $callback();
        }

        return Cache::remember($key, $this->getKonekoCacheTTL(), $callback);
    }

    /**
     * Elimina el valor de caché
     */
    protected function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Forzar almacenamiento de un valor por TTL
     */
    protected function put(string $key, mixed $value): void
    {
        Cache::put($key, $value, $this->getKonekoCacheTTL());
    }

    /**
     * Verifica si existe una clave
     */
    protected function has(string $key): bool
    {
        return Cache::has($key);
    }









    /**
     * TTL como DateTimeInterface.
     */
    protected function konekoCacheTtl(): DateTimeInterface
    {
        return now()->addMinutes($this->cacheTTL);
    }

    /**
     * Genera clave de caché consistente.
     */
    protected function makeKonekoCacheKey(string $key): string
    {
        return "koneko:{$this->cacheNamespace}:{$this->cacheGroup}:{$key}";
    }

    /**
     * Cachea o computa un valor.
     */
    protected function cacheKoneko(string $key, Closure $callback): mixed
    {
        if (!$this->cacheEnabled) {
            return $callback();
        }

        return Cache::remember($this->makeKonekoCacheKey($key), $this->konekoCacheTtl(), $callback);
    }

    /**
     * Cachea por usuario autenticado (requiere propiedad $user).
     */
    protected function cacheKonekoPerUser(Closure $callback): mixed
    {
        if (!property_exists($this, 'user') || !($this->user instanceof Authenticatable)) {
            throw new RuntimeException('Falta propiedad $user para cache per-user');
        }

        $key = 'user:' . $this->user->getAuthIdentifier();

        return $this->cacheKoneko($key, $callback);
    }

    /**
     * Borra un valor específico de caché.
     */
    protected function forgetKonekoCache(string $key): void
    {
        Cache::forget($this->makeKonekoCacheKey($key));
    }

    /**
     * Borra el caché asociado a un usuario.
     */
    protected function forgetKonekoCacheForUser(int|string $userId): void
    {
        $key = $this->makeKonekoCacheKey('user:' . $userId);
        Cache::forget($key);
    }

    /**
     * Construye clave de forma estática.
     */
    public static function buildKonekoCacheKey(string $namespace, string $group, string $key): string
    {
        return "koneko:{$namespace}:{$group}:{$key}";
    }
}
