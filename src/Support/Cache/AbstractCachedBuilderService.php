<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Cache;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use DateTimeInterface;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use RuntimeException;

/**
 * Clase abstracta para builders cacheables en el ecosistema Koneko.
 * Compatible con servicios de usuario y contextos globales.
 */
abstract class AbstractCachedBuilderService
{
    protected ?Authenticatable $user = null;

    protected string $component  = 'admin';
    protected string $group      = 'cache';
    protected string $cacheKey   = '';
    protected bool $isUserScoped = true;

    protected KonekoCacheManager $manager;

    public function __construct(?Authenticatable $user = null)
    {
        $this->user    = $user ?? Auth::user();
        $this->manager = new KonekoCacheManager($this->component, $this->group);

        if ($this->isUserScoped && !$this->user) {
            throw new RuntimeException("Se requiere un usuario para builders con scope de usuario.");
        }

        $this->cacheKey = $this->buildCacheKey();
    }

    /**
     * Método que debe construir y retornar los datos sin cache.
     */
    abstract protected function build(): array;

    /**
     * Retorna los datos, desde caché si está habilitado.
     */
    public function get(): array
    {
        if (!$this->manager->enabled()) {
            return $this->build();
        }

        return Cache::remember($this->cacheKey, $this->ttl(), fn () => $this->build());
    }

    /**
     * Fuerza la invalidación del caché.
     */
    public function forget(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * TTL como DateTimeInterface.
     */
    public function ttl(): DateTimeInterface
    {
        return now()->addMinutes($this->manager->ttl());
    }

    /**
     * Construye una clave única para el componente.
     */
    protected function buildCacheKey(): string
    {
        $key = $this->manager->path();

        if ($this->isUserScoped) {
            return "$key.user:{$this->user->getAuthIdentifier()}";
        }

        return "$key.global";
    }

    /**
     * Información útil de debug.
     */
    public function info(): array
    {
        return [
            'cache_key' => $this->cacheKey,
            'user_id'   => $this->user?->getAuthIdentifier(),
            ...$this->manager->info(),
        ];
    }
}
