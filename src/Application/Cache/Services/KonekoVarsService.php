<?php

namespace Koneko\VuexyAdmin\Application\Cache\Services;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;

/**
 * 🎛️ Servicio central para construcción de variables visuales Koneko.
 * Cachea resultados en memoria, permite resolución flexible desde settings + config.
 *
 * @method self context(string $group, string $section, string|null $subGroup = null)
 * @method self setKeyName(string $key)
 * @method self forScope(string $scope, int $scope_id)
 * @method self forModel(Model $model)
 * @method self forUser(Authenticatable|int|null $user)
 */
class KonekoVarsService
{
    protected string $namespace = CoreModule::NAMESPACE;
    protected string $component = CoreModule::COMPONENT;

    protected string $group;
    protected string $section;
    protected ?string $subGroup = null;
    protected ?string $keyName = null;

    protected ?string $scope     = null;
    protected ?int    $scope_id  = null;

    public function context(string $group, string $section, ?string $subGroup = null): static
    {
        $this->group     = $group;
        $this->section   = $section;
        $this->subGroup  = $subGroup;
        return $this;
    }

    public function setKeyName(string $key): static
    {
        $this->keyName = $key;
        return $this;
    }

    public function forScope(string $scope, int $scope_id): static
    {
        $this->scope    = $scope;
        $this->scope_id = $scope_id;
        return $this;
    }

    public function forModel(Model $model): static
    {
        $this->scope    = strtolower(class_basename($model));
        $this->scope_id = $model->getKey();
        return $this;
    }

    public function forUser(Authenticatable|int|null $user): static
    {
        $this->scope    = 'user';
        $this->scope_id = is_int($user) ? $user : ($user?->getAuthIdentifier());
        return $this;
    }

    /**
     * Ejecuta y cachea un resultado asociado al key/contexto actual
     *
     * @param string $key
     * @param Closure(): mixed $resolver
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, Closure $resolver, ?int $ttl = null): mixed
    {
        return $this->getCacheManager(['key_name' => $key])->remember($resolver, $ttl);
    }

    /**
     * Limpia el valor de caché actual según el contexto y clave
     */
    public function clear(): void
    {
        $this->getCacheManager()->forget();
    }

    /**
     * Devuelve el contexto actual aplicado
     */
    public function getContext(): array
    {
        return [
            'namespace' => $this->namespace,
            'component' => $this->component,
            'group'     => $this->group,
            'section'   => $this->section,
            'sub_group' => $this->subGroup,
            'scope'     => $this->scope,
            'scope_id'  => $this->scope_id,
            'key_name'  => $this->keyName,
        ];
    }

    /**
     * Devuelve la clave completa de caché calificada
     */
    public function cacheKey(?string $key = null): string
    {
        return $this->getCacheManager(['key_name' => $key ?? $this->keyName])->qualifiedKey();
    }

    /**
     * Obtiene el gestor de caché con el contexto aplicado
     */
    protected function getCacheManager(array $overrides = []): KonekoCacheManager
    {
        return cache_manager([
            'namespace' => $this->namespace,
            'component' => $this->component,
            'group'     => $this->group,
            'section'   => $this->section,
            'sub_group' => $this->subGroup,
            'scope'     => $this->scope,
            'scope_id'  => $this->scope_id,
            'key_name'  => $overrides['key_name'] ?? $this->keyName,
        ]);
    }
}
