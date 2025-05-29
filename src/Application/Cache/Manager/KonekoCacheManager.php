<?php

namespace Koneko\VuexyAdmin\Application\Cache\Manager;

use Illuminate\Support\Facades\Config;
use Koneko\VuexyAdmin\Application\Cache\Driver\KonekoCacheDriver;
use Koneko\VuexyAdmin\Application\Cache\Contracts\CacheRepositoryInterface;
use Koneko\VuexyAdmin\Application\Traits\System\Context\{HasBaseContext, HasCacheContextValidation};
use Koneko\VuexyAdmin\Application\CoreModule;

final class KonekoCacheManager implements CacheRepositoryInterface
{
    use HasBaseContext;
    use HasCacheContextValidation;

    public function __construct()
    {
        $this->setNamespace(CoreModule::NAMESPACE)
            ->setEnvironment()
            ->setComponent(CoreModule::COMPONENT);
    }

    // ==================== Factory ====================

    public static function make(): static
    {
        return new static();
    }

    // ======================= ⚙️ Configuración de Cache =========================
    public function isEnabled(): bool
    {
        foreach ($this->enabledCandidateKeys() as $key) {
            $value = Config::get($key);
            if (!is_null($value)) {
                return (bool) $value;
            }
        }

        return true;
    }

    public function resolveEnabledSourceKey(): string
    {
        foreach ($this->enabledCandidateKeys() as $key) {
            if (Config::has($key)) {
                return $key;
            }
        }

        return 'default';
    }

    public function resolveTTL(): int
    {
        foreach ($this->ttlCandidateKeys() as $key) {
            if (Config::has($key)) {
                return (int) Config::get($key);
            }
        }

        return 3600;
    }

    public function resolveTtlSourceKey(): string
    {
        foreach ($this->ttlCandidateKeys() as $key) {
            if (Config::has($key)) {
                return $key;
            }
        }

        return 'default';
    }

    public function driver(): string
    {
        return config('cache.default');
    }

    protected function enabledCandidateKeys(): array
    {
        $base = "{$this->context['namespace']}.{$this->context['component']}.{$this->context['group']}";

        return [
            "{$this->context['namespace']}.cache.enabled",
            "{$this->context['namespace']}.{$this->context['component']}.cache.enabled",
            "{$base}.cache.enabled",
            "{$base}.{$this->context['sub_group']}.cache.enabled",
            "{$base}.{$this->context['section']}.{$this->context['sub_group']}.cache.enabled",
        ];
    }

    protected function ttlCandidateKeys(): array
    {
        $base = "{$this->context['namespace']}.{$this->context['component']}.{$this->context['group']}";

        return [
            "{$base}.{$this->context['sub_group']}.ttl",
            "{$base}.ttl",
            "{$this->context['namespace']}.{$this->context['component']}.cache.ttl",
            "{$this->context['namespace']}.cache.ttl",
        ];
    }

    // ======================= 🧠 Operaciones =========================

    public function get(mixed $default = null): mixed
    {
        return KonekoCacheDriver::get($this->qualifiedKey(), $default);
    }

    public function put(mixed $value, ?int $ttl = null): void
    {
        KonekoCacheDriver::put($this->qualifiedKey(), $value, $ttl ?? $this->resolveTTL());
    }

    public function forget(): void
    {
        KonekoCacheDriver::forget($this->qualifiedKey());
    }

    public function remember(?callable $resolver = null, ?int $ttl = null): mixed
    {
        return $this->rememberWithTTLResolution($resolver ?? fn () => null, $ttl);
    }

    public function rememberWithTTLResolution(callable $resolver, ?int $ttl = null): mixed
    {
        return KonekoCacheDriver::remember(
            $this->qualifiedKey(),
            $ttl ?? $this->resolveTTL(),
            $resolver
        );
    }

    // ======================= 🧩 Contexto =========================

    public function has(string $qualifiedKey): bool
    {
        return KonekoCacheDriver::has($qualifiedKey);
    }

    public function hasContext(): bool
    {
        return isset($this->context['component'], $this->context['group'], $this->context['sub_group'], $this->context['key_name']);
    }

    public function reset(): static
    {

        return $this;
    }

    // ======================= 🧪 Diagnóstico =========================

    public function info(): array
    {
        return [
            'key'       => $this->qualifiedKey(),
            'context'   => $this->context,
            'enabled'   => $this->isEnabled(),
            'ttl'       => $this->resolveTTL(),
            'driver'    => $this->driver(),
        ];
    }

    public function infoWithCacheLayers(): array
    {
        return [
            'context'        => $this->context,
            'qualified_key'  => $this->qualifiedKey(),
            'enabled'        => $this->isEnabled(),
            'enabled_source' => $this->resolveEnabledSourceKey(),
            'ttl'            => $this->resolveTTL(),
            'ttl_source'     => $this->resolveTtlSourceKey(),
            'driver'         => $this->driver(),
            'has'            => $this->has($this->qualifiedKey()),
        ];
    }
}
