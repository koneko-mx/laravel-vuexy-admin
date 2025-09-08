<?php

namespace Koneko\VuexyAdmin\Application\Cache\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CacheRepositoryInterface
{
    // ==================== Factory ====================
    public static function make(array $context = []): static;

    // ==================== Context ====================
    public function namespace(?string $namespace): static;
    public function environment(?string $env = null): static;
    public function component(string $component): static;
    public function context(?string $group, ?string $section, ?string $subGroup = 'default'): static;
    public function ctx(string $path): static; // "group.section.sub"
    public function scope(Model|string|null $scope, ?int $id = null): static;
    public function scopeId(?int $scopeId): static;

    public function group(string $group): static;
    public function section(string $section): static;
    public function subGroup(string $subGroup): static;
    public function keyName(string $keyName): static;


    // ==================== Config ====================
    public function ttl(int $seconds): static;
    public function isEnabled(): bool;
    public function resolveTTL(): int;
    public function driver(): string;


    // ==================== Claves calif. / Scope ====================
    public function getQualifiedKey(?string $keyName = null): string;
    public function getScopeModel(): ?Model;


    // ==================== Cache Operations ====================
    public function put(mixed $value, ?int $ttl = null): void;
    public function get(?string $keyName = null, mixed $default = null): mixed;
    public function getMany(array $keyNames): array;
    public function setMany(array $kv, ?int $ttl=null): int;
    public function forget(?string $keyName = null): int;
    public function forgetByQualifiedKey(string $qualifiedKey): int;


    // ==================== Utils ====================
    public function hasKeyName(?string $keyName = null): bool;
    public function hasQualifiedKey(?string $qualifiedKey = null): bool;


    // ==================== Helpers ====================
    public function remember(callable $resolver, ?int $ttl = null): mixed;


    // ==================== Diagnostics ====================
    public function info(): array;
    public function infoWithCacheLayers(): array;
}
