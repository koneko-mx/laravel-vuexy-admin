<?php

namespace Koneko\VuexyAdmin\Application\Settings\Contracts;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface SettingsRepositoryInterface
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

    public function includeDisabled(bool $state = true): static;
    public function includeExpired(bool $state = true): static;
    public function asArray(bool $state = true): static;


    // ==================== Encryption ====================
    public function enableEncryption(bool $state = true): static;
    public function setEncryptionAlgorithm(string $algorithm = 'AES-256-CBC'): static;
    public function setEncryptionKey(?string $key = null): static;
    public function setEncryptionRotatedAt(DateTimeInterface|string|null $date): static;


    // ==================== Files ====================
    public function enableFile(bool $state = true): static;
    public function file(string $mime_type, string $file_name): static;
    public function mimeType(string $mime_type): static;
    public function fileName(string $file_name): static;
    public function handleFileUpload(UploadedFile $file, string $storageDisk = 'public'): static;


    // ==================== Markers ====================
    public function markAsActive(bool $state = true): static;
    public function expiresAt(DateTimeInterface|string|null $date): static;
    public function trackUsage(bool $state = true): static;
    public function setInternalConfigFlag(bool $state = true): static;


    // ==================== Metadata ====================
    public function description(string $description): static;
    public function hint(string $hint): static;


    // ==================== CRUD ====================
    public function set(string $keyName, mixed $value): void;
    public function setMany(array $kv): int;
    public function get(?string $keyName = null, mixed $default = null): mixed;
    public function getMany(array $keyNames, bool $decrypt = false): array;
    public function all(): Collection|array;


    // ==================== Deleters DB ====================
    public function deleteByKeyName(?string $keyName = null): int;
    public function deleteByQualifiedKey(string $qualifiedKey): int;
    public function deleteByContext(): int;
    public function deleteSubGroup(): int;
    public function deleteGroup(): int;
    public function deleteComponent(): int;

    // ==================== Claves calif. / Scope ====================
    public function getQualifiedKey(?string $keyName = null): string;
    public function getScopeModel(): ?Model;


    // ==================== Cache helpers ====================
    public function enableCache(bool $state = true): static;
    public function ttl(int $seconds): static;
    public function setCacheExpiresAt(DateTimeInterface|string|null $date): static;
    public function forgetCache(string|array $keys): int;
    public function remember(callable $resolver, ?int $ttl = null): mixed;
    public function bypassCache(bool $state = true): static;


    // ==================== Utils ====================
    public function hasKeyName(?string $keyName = null): bool;
    public function hasQualifiedKey(?string $qualifiedKey = null): bool;


    // ==================== Diagnostics ====================
    public function info(): array;
}
