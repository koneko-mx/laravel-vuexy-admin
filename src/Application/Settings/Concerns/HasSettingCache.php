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

    public function setCacheExpiresAt(Carbon|string|false|null $date): static
    {
        $this->attributes['is_should_cache'] = true;
        $this->cache['cache_expires_at'] = $date instanceof Carbon
            ? $date
            : ($date ? Carbon::parse($date) : null);

        return $this;
    }

    public function forgetCache(?string $keyName = null): static
    {
        $this->getCacheManager()
            ->setKeyName($keyName ?? $this->context['key_name'])
            ->forget();

        return $this;
    }

    public function remember(Closure $callback): mixed
    {
        $manager = $this->getCacheManager();

        // Desactivar cache o forzar bypass
        if (!$manager->isEnabled() || $this->bypassCache) {
            return $callback();
        }

        $key = $manager->qualifiedKey();
        $cached = KonekoCacheDriver::get($key);

        if (!is_null($cached)) {
            return $cached;
        }

        // Ejecutar callback y guardar en caché
        $value = $callback();

        // Si el valor es null, no lo cacheamos
        if (!is_null($value)) {
            $model = $this->queryForModel(); // <- Asegúrate de definirlo en el manager
            $ttl   = $model ? $this->resolveModelTTL($model, $manager) : $manager->resolveTTL();

            if ($ttl > 0) {
                $manager->put($value, $ttl);
            }
        }

        return $value;
    }

    public function cacheModel(?Setting $model = null): void
    {
        $model ??= $this->queryForModel();
        $manager = $this->getCacheManager();

        // validación
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

    public function getCacheManager(): KonekoCacheManager
    {
        return cache_m(
                $this->context['component'],
                $this->context['group'],
                $this->context['sub_group'],
            )
            ->setScope($this->context['scope'])
            ->setScopeId($this->context['scope_id'])
            ->setKeyName($this->context['key_name']);
    }

    // ==================== Helpers ====================

    protected function shouldCacheModel(Setting $model): bool
    {
        return $model->is_active
            && $model->is_should_cache
            && !$model->is_encrypted
            && !$model->is_sensitive;
    }

    protected function resolveModelTTL(Setting $model, KonekoCacheManager $manager): int
    {
        if ($model->cache_expires_at instanceof Carbon) {
            $ttl = now()->diffInMinutes($model->cache_expires_at, false);

            return $ttl > 0 ? $ttl : 0;
        }

        return $model->cache_ttl ?? $manager->resolveTTL();
    }
}
