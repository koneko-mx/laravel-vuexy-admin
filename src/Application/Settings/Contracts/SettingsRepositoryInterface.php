<?php

namespace Koneko\VuexyAdmin\Application\Settings\Contracts;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Application\Settings\SettingDefaults;
use Koneko\VuexyAdmin\Models\Setting;

interface SettingsRepositoryInterface
{
    // ==================== Factory ====================

    public static function make(): static;
    public static function fromArray(array $context): static;
    public static function fromRequest(?Request $request = null): static;

    // ==================== Context ====================

    public function setEnvironment(string $environment): static;
    public function setComponent(string $component): static;

    public function context(?string $group = null, ?string $section, ?string $subGroup = 'default'): static;
    public function setContextArray(array $context): static;

    public function setScope(Model|string|false $scope, int|null|false $scopeId = false): static;
    public function setScopeId(?int $scopeId): static;
    public function setUser(Authenticatable|int|null|false $user): static;
    public function withScopeFromModel(Model $model): static;

    public function setGroup(string $group): static;
    public function setSection(string $section): static;
    public function setSubGroup(string $subGroup): static;
    public function setKeyName(string $keyName): static;

    public function includeDisabled(bool $state = true): static;
    public function includeExpired(bool $state = true): static;
    public function bypassCache(bool $state = true): static;
    public function asArray(bool $state = true): static;

    // ==================== Encryption ====================

    public function enableEncryption(bool $state = true): static;
    public function setEncryption(string $algorithm = SettingDefaults::DEFAULT_ALGORITHM, ?string $key = null): static;
    public function setEncryptionAlgorithm(string $algorithm): static;
    public function setEncryptionKey(string $key): static;
    public function setEncryptionRotatedAt(Carbon|string|false|null $date): static;

    // ==================== Files ====================

    public function enableFile(bool $state = true): static;
    public function setFile(string $mime_type, string $file_name): static;
    public function setMimeType(string $mime_type): static;
    public function setFileName(string $file_name): static;
    public function handleFileUpload(UploadedFile $file, string $storageDisk = 'public'): static;

    // ==================== Markers ====================

    public function markAsSystem(bool $state = true): static;
    public function markAsSensitive(bool $state = true): static;
    public function markAsEditable(bool $state = true): static;
    public function markAsActive(bool $state = true): static;
    public function expiresAt(Carbon|string|false|null $date): static;
    public function trackUsage(bool $state = true): static;
    public function setInternalConfigFlag(bool $state = true): static;

    // ==================== Metadata ====================

    public function setDescription(string $description): static;
    public function setHint(string $hint): static;

    // ==================== CRUD ====================

    public function set(mixed $value, ?string $keyName = null): void;
    public function get(?string $keyName = null, mixed $default = null): mixed;
    public function delete(string $qualifiedKey): void;
    public function all(): Collection|array;

    public function deleteByContext(): int;
    public function deleteGroup(): int;
    public function deleteSubGroup(): int;

    // ==================== Fetchers ====================

    public function getGroup(bool $asArray = false): Collection|array;
    public function getSubGroup(bool $asArray = false): Collection|array;
    public function getComponents(bool $asArray = false): Collection|array;
    public function getGroups(bool $asArray = false): Collection|array;
    public function getSubGroups(bool $asArray = false): Collection|array;

    // ==================== Cache ====================

    public function enableCache(bool $state = true): static;
    public function setCacheTTL(int $seconds): static;
    public function setCacheExpiresAt(Carbon|string|false|null $date): static;
    public function cacheModel(?Setting $model = null): void;
    public function forgetCache(?string $keyName = null): static;
    public function remember(Closure $callback): mixed;


    // ==================== Getters ====================

    public function qualifiedKey(?string $key = null): string;
    public function exists(string $qualifiedKey): bool;
    public function existsByContext(): bool;
    public function isUsable(): bool;
    public function getScopeModel(): ?Model;

    // ==================== Utils ====================

    public function has(string $qualifiedKey): bool;
    public function hasContext(): bool;
    public function setInactiveByContext(): int;
    public function reset(): void;

    // ==================== Diagnostics ====================

    public function info(): array;
}
