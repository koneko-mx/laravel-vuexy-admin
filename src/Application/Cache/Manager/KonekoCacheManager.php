<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Cache\Manager;

use InvalidArgumentException;
use Illuminate\Support\Facades\Config;
use Koneko\VuexyAdmin\Application\Cache\Driver\KonekoCacheDriver;
use Koneko\VuexyAdmin\Application\Cache\Contracts\CacheRepositoryInterface;
use Koneko\VuexyAdmin\Application\Cache\Builders\SettingCacheKeyBuilder;
use Koneko\VuexyAdmin\Application\Traits\System\Context\{HasBaseContext, HasCacheContextValidation};

final class KonekoCacheManager implements CacheRepositoryInterface
{
    use HasBaseContext;
    use HasCacheContextValidation;

    /** TTL override (segundos). Si es null, se resuelve desde config. */
    private ?int $ttlOverride = null;

    public function __construct()
    {
        $namespace = config('koneko.namespace', 'koneko');
        $this->namespace($namespace)->environment();
    }

    // ==================== Factory ====================

    public static function make(array $context = []): static
    {
        $i = new static();
        if ($context) {
            $i->setContextArray($context);
        }
        return $i;
    }

    // ==================== Context (API del contrato) ====================

    // component(), context(), ctx(), scope(), scopeId(), group(), section(), subGroup(), keyName()
    // provienen de HasBaseContext

    // ==================== Config ====================

    public function ttl(int $seconds): static
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('El TTL no puede ser negativo.');
        }
        $this->ttlOverride = $seconds;
        return $this;
    }

    public function isEnabled(): bool
    {
        foreach ($this->enabledCandidateKeys() as $key) {
            $value = Config::get($key);
            if ($value !== null) {
                return (bool) $value;
            }
        }
        return true; // habilitado por defecto
    }

    public function resolveTTL(): int
    {
        if ($this->ttlOverride !== null) {
            return $this->ttlOverride;
        }

        foreach ($this->ttlCandidateKeys() as $key) {
            if (Config::has($key)) {
                return (int) Config::get($key);
            }
        }
        return 3600; // 1 h por defecto
    }

    public function driver(): string
    {
        return (string) config('cache.default');
    }

    protected function enabledCandidateKeys(): array
    {
        $ns   = $this->context['namespace']   ?? 'app';
        $comp = $this->context['component']   ?? 'core';
        $grp  = $this->context['group']       ?? 'default';
        $sec  = $this->context['section']     ?? 'default';
        $sub  = $this->context['sub_group']   ?? 'default';

        $base = "$ns.$comp.$grp";

        return [
            "$ns.cache.enabled",
            "$ns.$comp.cache.enabled",
            "$base.cache.enabled",
            "$base.$sub.cache.enabled",
            "$base.$sec.$sub.cache.enabled",
        ];
    }

    protected function ttlCandidateKeys(): array
    {
        $ns   = $this->context['namespace']   ?? 'app';
        $comp = $this->context['component']   ?? 'core';
        $grp  = $this->context['group']       ?? 'default';
        $sub  = $this->context['sub_group']   ?? 'default';

        $base = "$ns.$comp.$grp";

        return [
            "$base.$sub.ttl",
            "$base.ttl",
            "$ns.$comp.cache.ttl",
            "$ns.cache.ttl",
        ];
    }

    // ==================== Claves calificadas / Scope ====================

    public function getQualifiedKey(?string $keyName = null): string
    {
        return $this->buildQualifiedKey($keyName ?? $this->context['key_name'] ?? null);
    }

    // getScopeModel() viene del trait HasBaseContext

    // ==================== Cache Operations ====================

    public function put(mixed $value, ?int $ttl = null): void
    {
        $key = $this->getQualifiedKey();
        $ttl = $ttl ?? $this->resolveTTL();
        if ($ttl <= 0) {
            return; // no cachear
        }
        KonekoCacheDriver::put($key, $value, $ttl);
    }

    public function get(?string $keyName = null, mixed $default = null): mixed
    {
        $key = $this->getQualifiedKey($keyName);
        return KonekoCacheDriver::get($key, $default);
    }

    public function getMany(array $keyNames): array
    {
        if (!$keyNames) return [];
        $out = [];
        foreach ($keyNames as $name) {
            $qualified = $this->buildQualifiedKey((string) $name);
            $out[$name] = KonekoCacheDriver::get($qualified);
        }
        return $out;
    }

    public function setMany(array $kv, ?int $ttl = null): int
    {
        if (!$kv) return 0;

        $ttl = $ttl ?? $this->resolveTTL();
        if ($ttl <= 0) return 0;

        $n = 0;
        foreach ($kv as $name => $value) {
            $qualified = $this->buildQualifiedKey((string) $name);
            KonekoCacheDriver::put($qualified, $value, $ttl);
            $n++;
        }
        return $n;
    }

    public function forget(?string $keyName = null): int
    {
        $qualified = $this->getQualifiedKey($keyName);
        return KonekoCacheDriver::forget($qualified) ? 1 : 0;
    }

    public function forgetByQualifiedKey(string $qualifiedKey): int
    {
        return KonekoCacheDriver::forget($qualifiedKey) ? 1 : 0;
    }

    // ==================== Utils ====================

    public function hasKeyName(?string $keyName = null): bool
    {
        $qualified = $this->getQualifiedKey($keyName);
        return KonekoCacheDriver::has($qualified);
    }

    public function hasQualifiedKey(?string $qualifiedKey = null): bool
    {
        $qualifiedKey ??= $this->getQualifiedKey();
        return KonekoCacheDriver::has($qualifiedKey);
    }

    // ==================== Helpers ====================

    public function remember(callable $resolver, ?int $ttl = null): mixed
    {
        $key = $this->getQualifiedKey();
        $ttl = $ttl ?? $this->resolveTTL();

        if ($ttl <= 0) {
            // sin cache
            return $resolver();
        }

        return KonekoCacheDriver::remember($key, $ttl, $resolver(...));
    }

    // ==================== Diagnostics ====================

    public function info(): array
    {
        return [
            'context'       => $this->context,
            'qualified_key' => $this->getQualifiedKey(),
            'enabled'       => $this->isEnabled(),
            'ttl'           => $this->resolveTTL(),
            'driver'        => $this->driver(),
        ];
    }

    public function infoWithCacheLayers(): array
    {
        return [
            'context'        => $this->context,
            'qualified_key'  => $this->getQualifiedKey(),
            'enabled'        => $this->isEnabled(),
            'enabled_source' => $this->resolveEnabledSourceKey(),
            'ttl'            => $this->resolveTTL(),
            'ttl_source'     => $this->resolveTtlSourceKey(),
            'driver'         => $this->driver(),
            'has'            => $this->hasQualifiedKey(),
        ];
    }

    // ==================== Internals ====================

    /**
     * Construye la clave calificada SIN mutar el contexto.
     * Acepta un $keyName ad-hoc o usa el del contexto si existe.
     */
    private function buildQualifiedKey(?string $keyName): string
    {
        // Validaciones de contexto mínimas
        $this->validateBaseContext();
        $this->validateScopeContext($this->context['scope'], $this->context['scope_id']);

        $keyName = $keyName ?? $this->context['key_name'] ?? null;
        if ($keyName === null || $keyName === '') {
            throw new InvalidArgumentException("Falta 'key_name' para construir la clave calificada.");
        }

        return SettingCacheKeyBuilder::build(
            $this->context['namespace'],
            $this->context['environment'],
            $this->context['scope'],
            $this->context['scope_id'],
            $this->context['component'],
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $keyName,
        );
    }

    protected function resolveEnabledSourceKey(): string
    {
        foreach ($this->enabledCandidateKeys() as $key) {
            if (Config::has($key)) {
                return $key;
            }
        }
        return 'default';
    }

    protected function resolveTtlSourceKey(): string
    {
        if ($this->ttlOverride !== null) {
            return 'override';
        }
        foreach ($this->ttlCandidateKeys() as $key) {
            if (Config::has($key)) {
                return $key;
            }
        }
        return 'default';
    }
}
