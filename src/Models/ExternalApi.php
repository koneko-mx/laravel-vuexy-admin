<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Application\Enums\ExternalApi\{
    ApiProvider,
    ApiAuthType,
    ApiEnvironment
};

/**
 * Modelo que representa una integración externa (API) dentro del ERP Koneko.
 *
 * Puede ser utilizada para registrar servicios como Google Analytics, Banxico, SAT, etc.
 */
class ExternalApi extends Model
{
    // ===================== CONFIGURACIÓN =====================

    protected $table = 'external_apis';

    protected $fillable = [
        'name',
        'slug',
        'module',
        'provider',
        'base_url',
        'doc_url',
        'auth_type',
        'credentials',
        'scopes',
        'is_active',
        'environment',
        'metadata',
        'config',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'display_name',
        'full_name',
        'is_production',
    ];

    protected $casts = [
        'is_active'   => 'bool',
        'credentials' => 'array',
        'scopes'      => 'array',
        'metadata'    => 'array',
        'config'      => 'array',
        'provider'    => ApiProvider::class,
        'auth_type'   => ApiAuthType::class,
        'environment' => ApiEnvironment::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (ExternalApi $api) {
            if (empty($api->slug)) {
                $api->slug = Str::slug($api->name);
            }
        });
    }

    // ===================== RELACIONES =====================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ===================== ACCESORES =====================

    public function getDisplayName(): string
    {
        return "{$this->provider?->label()}: {$this->name}";
    }

    public function getFullNameAttribute(): string
    {
        return "[{$this->module}] {$this->name}";
    }

    public function getIsProduction(): bool
    {
        return $this->environment === ApiEnvironment::Production;
    }

    // ===================== HELPERS =====================

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes ?? [], true);
    }

    public function getCredential(string $key, mixed $default = null): mixed
    {
        return $this->credentials[$key] ?? $default;
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function isOauth(): bool
    {
        return $this->auth_type === ApiAuthType::OAuth2;
    }

    public function isApiKey(): bool
    {
        return $this->auth_type === ApiAuthType::ApiKey;
    }

    public function isJwt(): bool
    {
        return $this->auth_type === ApiAuthType::JWT;
    }
}
