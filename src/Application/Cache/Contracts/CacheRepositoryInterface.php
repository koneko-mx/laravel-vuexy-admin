<?php

namespace Koneko\VuexyAdmin\Application\Cache\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface CacheRepositoryInterface
{
    // ==================== Factory ====================

    public static function make(): static;
    public static function fromArray(array $context): static;
    public static function fromRequest(?Request $request = null): static;

    // ==================== Context ====================

    public function environment(?string $environment = null): static;
    public function component(string $component): static;

    public function context(?string $group, ?string $section, ?string $subGroup = 'default'): static;
    public function setContextArray(array $context): static;

    public function scope(Model|string|false $scope, int|null|false $scopeId = false): static;
    public function scopeId(?int $scopeId): static;
    public function user(Authenticatable|int|null|false $user): static;
    public function withScopeFromModel(Model $model): static;

    public function group(string $group): static;
    public function section(string $section): static;
    public function subGroup(string $subGroup): static;
    public function keyName(string $keyName): static;

    // ==================== Config ====================

    public function isEnabled(): bool;
    public function resolveTTL(): int;
    public function driver(): string;

    // ==================== Cache Operations ====================

    public function get(mixed $default = null): mixed;
    public function put(mixed $value, ?int $ttl = null): void;
    public function forget(): void;

    public function remember(?callable $resolver = null, ?int $ttl = null): mixed;
    public function rememberWithTTLResolution(callable $resolver, ?int $ttl = null): mixed;

    // ==================== Getters ====================

    public function getQualifiedKey(?string $key = null): string;
    public function getScopeModel(): ?Model;

    // ==================== Utils ====================

    public function has(string $qualifiedKey): bool;
    public function hasContext(): bool;
    public function reset(): static;


    // ==================== Diagnostics ====================

    public function info(): array;
    public function infoWithCacheLayers(): array;
}
