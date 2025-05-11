<?php

namespace Koneko\VuexyAdmin\Support\Cache;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth, Cache};
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingScope;
use Koneko\VuexyAdmin\Models\User;

/**
 * 🧠 Builder base cacheable especializado para estructuras clave-valor como Settings.
 * Compatible con cache global o por usuario.
 */
abstract class AbstractKeyValueCacheBuilder
{
    private const DEFAULT_SCOPE    = SettingScope::GLOBAL->value;
    private const USER_GUEST_ALIAS = SettingScope::GUEST->value;

    protected string $namespace;
    protected string $scope;

    protected string $component;
    protected ?string $module = null;
    protected string $group;
    protected string $subGroup;

    protected bool $isUserScoped = false;

    protected ?Authenticatable $user = null;
    protected KonekoCacheManager $manager;

    /**
     * Establece el contexto completo de la caché.
     */
    public function setContext(
        string $component,
        string $group,
        string $subGroup,
        string $scope = self::DEFAULT_SCOPE
    ): static {
        $this->manager = cache_manager($component, $group, $subGroup, $scope);

        return $this
            ->setComponent($this->manager->currentComponent())
            ->setGroup($this->manager->currentGroup())
            ->setSubGroup($this->manager->currentSubGroup())
            ->setScope($scope);
    }

    public function setComponent(string $component): static
    {
        return $this->setContext(
            component: $component,
            group: $this->group,
            subGroup: $this->subGroup,
            scope: $this->scope ?? self::DEFAULT_SCOPE
        );
    }

    public function setGroup(string $group): static
    {
        return $this->setContext(
            component: $this->component,
            group: $group,
            subGroup: $this->subGroup,
            scope: $this->scope ?? self::DEFAULT_SCOPE
        );
    }

    public function setSubGroup(string $subGroup): static
    {
        return $this->setContext(
            component: $this->component,
            group: $this->group,
            subGroup: $subGroup,
            scope: $this->scope ?? self::DEFAULT_SCOPE
        );
    }

    public function setScope(SettingScope|string $scope): static
    {
        return $this->setContext(
            component: $this->component,
            group: $this->group,
            subGroup: $this->subGroup,
            scope: is_string($scope) ? SettingScope::fromOrFail(strtolower($scope))->value : $scope->value
        );
    }

    public function setUser(int|Authenticatable|null|false $user = null): static
    {
        match (true) {
            $user === false => $this->resetUserScope(),
            is_int($user)   => $this->assignUser(User::findOrFail($user)),
            $user instanceof Authenticatable => $this->assignUser($user),
            default         => $this->assignUser(Auth::user())
        };

        return $this;
    }


    protected function assignUser(?Authenticatable $user): void
    {
        $this->user = $user;
        $this->isUserScoped = !is_null($user);
    }

    protected function resetUserScope(): void
    {
        $this->user = null;
        $this->isUserScoped = false;
    }

    protected function refreshContext(): static
    {
        return $this->setContext(
            component: $this->component,
            group: $this->group,
            subGroup: $this->subGroup,
            scope: $this->scope ?? self::DEFAULT_SCOPE
        );
    }

    public function currentNamespace(): string
    {
        return $this->namespace;
    }

    public function currentComponent(): string
    {
        return $this->component;
    }

    public function currentGroup(): string
    {
        return $this->group;
    }

    public function currentSubGroup(): string
    {
        return $this->subGroup;
    }

    public function currentScope(): string
    {
        return $this->scope;
    }

    public function currentUser(): ?Authenticatable
    {
        return $this->user;
    }

    public function currentUserId(): string
    {
        return $this->user?->getAuthIdentifier() ?? self::USER_GUEST_ALIAS;
    }

    public function isUserScoped(): bool
    {
        return $this->isUserScoped;
    }

    protected function rememberCache(string $cacheKey, callable $callback): mixed
    {
        $this->validateContext();

        $key = $this->manager->key($cacheKey);

        if (!$this->manager->enabled()) {
            return $callback();
        }

        return Cache::remember($key, $this->manager->ttl(), $callback);
    }

    protected function forgetCache(string $cacheKey): void
    {
        $this->validateContext();
        Cache::forget($this->manager->key($cacheKey));
    }

    protected function generateCacheKey(string $cacheKey): string
    {
        $this->validateContext();

        $userSegment = $this->isUserScoped ?
            'u.' . $this->currentUserId() :
            self::DEFAULT_SCOPE;

        $scopeSegment = $this->scope === self::DEFAULT_SCOPE
            ? null
            : "scope." . SettingScope::from($this->scope)->value;

        $base = implode('.', array_filter([
            $this->namespace,
            app()->environment(),
            $this->component,
            $this->group,
            $this->subGroup,
            $scopeSegment,
            $userSegment,
            $cacheKey
        ]));

        return strlen($base) > KonekoCacheManager::MAX_KEY_LENGTH
            ? 'h:' . crc32($base)
            : $base;
    }

    protected function validateContext(): void
    {
        if ($this->component !== 'project') {
            $module = KonekoModuleRegistry::get($this->component);

            if (!$module) {
                throw new \InvalidArgumentException("El componente '{$this->component}' no está registrado en KonekoModuleRegistry.");
            }

            $this->module = $module->composerName;

        } else {
            $this->module = null;
        }
    }

    protected function validateKey(string $key): void
    {
        if (!preg_match('/^[a-z0-9\._\-]+$/', $key)) {
            throw new \InvalidArgumentException("La clave '{$key}' no es válida.");
        }
    }

    public function cacheInfo(string $cacheKey): array
    {
        return [
            'key'       => $this->generateCacheKey($cacheKey),
            'enabled'   => $this->manager->enabled(),
            'ttl'       => $this->manager->ttl(),
            'user'      => $this->user?->getAuthIdentifier(),
            'driver'    => config('cache.default'),
            'component' => $this->component,
            'group'     => $this->group,
            'scoped'    => $this->isUserScoped,
        ];
    }
}
