<?php

namespace Koneko\VuexyAdmin\Application\Config\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface ConfigRepositoryInterface
{
    // ==================== Factory ====================

    public static function make(): static;
    public static function fromArray(array $context): static;
    public static function fromRequest(?Request $request = null): static;

    // ==================== Context ====================

    public function setNamespace(string $namespace): static;
    public function setEnvironment(?string $environment = null): static;
    public function setComponent(string $component): static;

    public function context(string $group, ?string $section = null, ?string $subGroup = null): static;
    public function setContextArray(array $context): static;

    public function setScope(Model|string|false $scope, int|null|false $scopeId = false): static;
    public function setScopeId(?int $scopeId): static;
    public function setUser(Authenticatable|int|null|false $user): static;
    public function withScopeFromModel(Model $model): static;

    public function setGroup(string $group): static;
    public function setSection(string $section): static;
    public function setSubGroup(string $subGroup): static;
    public function setKeyName(string $keyName): static;

    // ==================== Getters ====================

    public function get(string $qualifiedKey, mixed $default = null): mixed;
    public function fromDb(bool $fromDb = true): static;
    public function sourceOf(string $qualifiedKeySufix): ?string;

    public function qualifiedKey(?string $key = null): string;
    public function getScopeModel(): ?Model;

    // ==================== Advanced ====================

    public function syncFromRegistry(string $configKey, bool $forceReload = false): static;

    /*
    public function loadAll(): array;
    public function loadByContext(): array;
    public function rememberConfig(Closure $callback): mixed;
    */

    // ==================== Utils ====================

    public function has(string $qualifiedKey): bool;
    //public function hasContext(): bool;
    public function reset(): static;

    // ==================== Diagnostics ====================

    public function info(): array;
}
