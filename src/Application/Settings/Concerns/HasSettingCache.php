<?php

namespace Koneko\VuexyAdmin\Application\Settings\Concerns;

use Closure;
use Carbon\Carbon;
use Koneko\VuexyAdmin\Application\Cache\Driver\KonekoCacheDriver;
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\VuexyAdmin\Models\Setting;

trait HasSettingCache
{
    protected array $cache = [
        'cache_ttl'        => null,
        'cache_expires_at' => null,
    ];

    public function enableCache(bool $state = true): static
    {
        $this->attributes['is_should_cache'] = $state;
        return $this;
    }

    public function setCacheTTL(int $seconds): static
    {
        $this->attributes['is_should_cache'] = true;
        $this->cache['cache_ttl'] = $seconds;
        return $this;
    }

    public function ttl(int $seconds): static
    {
        return $this->setCacheTTL($seconds);
    }

    public function setCacheExpiresAt(\DateTimeInterface|string|null $date): static
    {
        $this->attributes['is_should_cache'] = true;
        $this->cache['cache_expires_at'] = $date instanceof Carbon
            ? $date
            : ($date ? Carbon::parse($date) : null);

        return $this;
    }

    /**
     * Olvida entradas de caché por key_name (NO borra DB).
     * Retorna cuántas claves de cache se invalidaron.
     */
    public function forgetCache(string|array $keys): int
    {
        $n = 0;
        foreach (array_filter((array) $keys, fn($k) => $k !== null && $k !== '') as $k) {
            // Suma solo si realmente se invalidó (el manager devuelve 1/0)
            $n += (int) $this->getCacheManager()->keyName((string) $k)->forget();
        }
        return $n;
    }

    public function remember(callable $resolver, ?int $ttl = null): mixed
    {
        $manager = $this->getCacheManager();

        // Sin caché: deshabilitada, bypass, o TTL explícito ≤ 0
        if (
            !$manager->isEnabled()
            || ($this->bypassCache ?? false)
            || ($ttl !== null && $ttl <= 0)
        ) {
            return $resolver();
        }

        $key    = $manager->getQualifiedKey();
        $cached = KonekoCacheDriver::get($key);

        if ($cached !== null) {
            return $cached;
        }

        // Ejecutar el resolvedor
        $value = $resolver();

        // No cachear null
        if ($value === null) {
            return $value;
        }

        // Resolver TTL: prioridad al TTL explícito; luego al del modelo; luego al manager
        $modelTTL = null;
        if (method_exists($this, 'queryForModel')) {
            $model = $this->queryForModel();
            if ($model && method_exists($this, 'resolveModelTTL')) {
                $modelTTL = $this->resolveModelTTL($model, $manager);
            }
        }

        $finalTtl = $ttl ?? $modelTTL ?? $manager->resolveTTL();

        if ($finalTtl > 0) {
            $manager->put($value, $finalTtl);
        }

        return $value;
    }

    public function cacheModel(?Setting $model = null): void
    {
        $model   ??= $this->queryForModel();
        $manager = $this->getCacheManager();

        if (!$manager->isEnabled()) {
            $manager->forget();
            return;
        }

        if ($model && $this->shouldCacheModel($model)) {
            $ttl = $this->resolveModelTTL($model, $manager);
            if ($ttl > 0) {
                $manager->put($model->value, $ttl);
            }
        } else {
            $manager->forget();
        }
    }

    // ==================== Helpers ====================

    protected function shouldCacheModel(Setting $model): bool
    {
        return $model->is_active
            && $model->is_should_cache
            && !$model->is_encrypted;
    }

    protected function resolveModelTTL(Setting $model, KonekoCacheManager $manager): int
    {
        if ($model->cache_expires_at instanceof Carbon) {
            $ttl = now()->diffInSeconds($model->cache_expires_at, false);
            return $ttl > 0 ? $ttl : 0;
        }

        return $model->cache_ttl ?? $manager->resolveTTL();
    }

    /**
     * Debe existir en el manager que use este trait.
     * @return KonekoCacheManager
     */
    abstract public function getCacheManager();
    abstract public function queryForModel(): ?\Illuminate\Database\Eloquent\Model;
}
