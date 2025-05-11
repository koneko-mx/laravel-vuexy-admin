<?php

namespace Koneko\VuexyAdmin\Application\Cache;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth, Config};
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingScope;
use Koneko\VuexyAdmin\Models\User;

class KonekoCacheManager
{
    public const MAX_KEY_LENGTH = 120; // Friendly limit
    private const DEFAULT_SCOPE    = SettingScope::GLOBAL->value;
    private const USER_GUEST_ALIAS = SettingScope::GUEST->value;

    private string $namespace;
    private string $scope;

    private string $component;
    private string $group;
    private string $subGroup;

    protected bool $isUserScoped = false;
    protected ?Authenticatable $user = null;

    public function __construct(string $namespace)
    {
        if (empty($namespace) || !preg_match('/^[a-z0-9\-]+$/', $namespace)) {
            throw new \InvalidArgumentException("El namespace '{$namespace}' no es válido.");
        }

        $this->namespace = $this->truncate('namespace', $namespace, 8);
        $this->scope = self::DEFAULT_SCOPE;
    }


    // ========= CONTEXT MANAGEMENT =========

    public function setContext(
        string $component,
        string $group,
        string $subGroup,
        string $scope = self::DEFAULT_SCOPE
    ): static {
        return $this
            ->setComponent($component)
            ->setGroup($group)
            ->setSubGroup($subGroup)
            ->setScope($scope);
    }

    public function setScope(SettingScope|string $scope): static
    {
        if (is_string($scope) && !SettingScope::isValid($scope)) {
            throw new \InvalidArgumentException("Scope '{$scope}' no es válido.");
        }

        $this->scope = is_string($scope)
            ? SettingScope::from($scope)->value
            : $scope->value;

        return $this;
    }


    public function setNamespace(string $namespace): static
    {
        $this->namespace = $this->truncate('namespace', $namespace, 8);

        return $this;
    }

    public function setComponent(string $component): static
    {
        $this->component = $this->truncate('component', $component, 16);

        return $this;
    }

    public function setGroup(string $group): static
    {
        $this->group = $this->truncate('group', $group, 16);

        return $this;
    }

    public function setSubGroup(string $subGroup): static
    {
        $this->subGroup = $this->truncate('subGroup', $subGroup, 16);

        return $this;
    }

    public function setUser(int|Authenticatable|null|false $user = null): static
    {
        match (true) {
            $user === false => $this->resetUserScope(),             // Visitante explícito
            is_int($user)   => $this->assignUser(User::findOrFail($user)),
            $user instanceof Authenticatable => $this->assignUser($user),
            default         => $this->assignUser(Auth::user())      // Usuario autenticado o null (visitante)
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

    public function isUserScoped(): bool
    {
        return $this->isUserScoped;
    }

    // ========= ACCESSORS =========

    public function currentNamespace(): string  { return $this->namespace; }
    public function currentComponent(): string  { return $this->component; }
    public function currentGroup(): string      { return $this->group; }
    public function currentSubGroup(): string   { return $this->subGroup; }
    public function currentScope(): string      { return $this->scope; }

    // ========= CACHE CONFIGURATION =========

    public function key(string $suffix): string
    {
        $this->ensureContext();

        $userSegment = $this->isUserScoped
            ? 'u.' . ($this->user?->getAuthIdentifier() ?? self::USER_GUEST_ALIAS)
            : self::DEFAULT_SCOPE;

        $scopeSegment = $this->scope === self::DEFAULT_SCOPE
            ? null
            : "scope." . $this->scope;

        $base = implode('.', array_filter([
            $this->namespace,
            app()->environment(),
            $this->component,
            $this->group,
            $this->subGroup,
            $scopeSegment,
            $userSegment,
            $suffix
        ]));

        return strlen($base) > self::MAX_KEY_LENGTH
            ? 'h:' . crc32($base)
            : $base;
    }

    public function ttl(): int
    {
        return (int) (
            Config::get("{$this->path()}.ttl") ??
            Config::get("{$this->namespace}.{$this->component}.{$this->group}.ttl") ??
            Config::get("{$this->namespace}.{$this->component}.cache.ttl") ??
            Config::get("{$this->namespace}.cache.ttl", 3600)
        );
    }

    public function enabled(): bool
    {
        return (bool) (
            Config::get("{$this->path()}.enabled") ??
            Config::get("{$this->namespace}.{$this->component}.{$this->group}.enabled") ??
            Config::get("{$this->namespace}.{$this->component}.cache.enabled") ??
            Config::get("{$this->namespace}.cache.enabled", true)
        );
    }

    public function driver(): string
    {
        return Config::get('cache.default');
    }

    public function path(): string
    {
        return "{$this->namespace}." . app()->environment() . ".{$this->component}.{$this->group}.{$this->subGroup}";
    }

    public function info(): array
    {
        return [
            'environment' => app()->environment(),
            'namespace'   => $this->namespace,
            'component'   => $this->component,
            'group'       => $this->group,
            'subGroup'    => $this->subGroup,
            'scope'       => $this->scope,
            'enabled'     => $this->enabled(),
            'ttl'         => $this->ttl(),
            'driver'      => $this->driver(),
        ];
    }

    // ========= VALIDATION & SANITIZATION =========

    private function validateSlug(string $field, string $value): void
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            throw new \InvalidArgumentException("El valor de '{$field}' debe ser un slug válido.");
        }
    }

    private function ensureContext(): void
    {
        foreach (['namespace', 'component', 'group', 'subGroup'] as $prop) {
            if (empty($this->$prop) || !preg_match('/^[a-z0-9\-]+$/', $this->$prop)) {
                throw new \InvalidArgumentException("El valor de '{$prop}' es obligatorio y debe ser un slug válido.");
            }
        }

    }

    private function truncate(string $field, string $value, int $maxLength): string
    {
        $this->validateSlug($field, $value);

        return Str::limit(strtolower($value), $maxLength, '');
    }
}
