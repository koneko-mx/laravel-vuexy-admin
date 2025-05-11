<?php

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\Cache;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoComponentContextRegistrar;
use Koneko\VuexyAdmin\Application\Cache\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Settings\SettingsGroupCollection;
use Koneko\VuexyAdmin\Models\Setting;
use Illuminate\Support\Str;

/**
 * 📊 Gestor extendido de Settings del ecosistema Koneko.
 * Soporte para componentes, namespaces, TTL inteligente y helper fluido.
 */
class ____KonekoSettingManager implements SettingsRepositoryInterface
{
    protected string $component;
    protected string $namespace;
    protected ?string $moduleSlug = null;

    public function __construct(?string $component = null)
    {
        $this->component = $component ?? KonekoComponentContextRegistrar::currentComponent() ?? 'admin';
        $this->namespace = $this->component . '.'; // Default to root namespace
    }

    public function setDefaultNamespace(string $namespace, ?string $slug = null): self
    {
        $this->namespace = rtrim($namespace, '.') . '.';
        $this->moduleSlug = $slug;
        return $this;
    }

    public function self(?string $subNamespace = null): self
    {
        return $this->in($this->namespace . ltrim((string) $subNamespace, '.') . '.');
    }

    public function in(string $namespace): self
    {
        $this->namespace = rtrim($namespace, '.') . '.';
        return $this;
    }

    public function get(string $key, ...$args): mixed
    {
        $fullKey = $this->qualifyKey($key);
        $cache = new KonekoCacheManager($this->component, 'settings');

        if (! $cache->enabled()) {
            return $this->query($fullKey);
        }

        return Cache::remember($cache->key(md5($fullKey)), $cache->ttl(), fn () => $this->query($fullKey));
    }

    public function getGroup(?string $prefix = ''): array
    {
        $query = Setting::where('key', 'like', $this->namespace . '%');

        if ($prefix) {
            $query->where('key', 'like', $this->namespace . trim($prefix, '.') . '.%');
        }

        return $query->pluck('value', 'key')->mapWithKeys(function ($val, $key) {
            $shortKey = Str::after($key, $this->namespace);
            return [$shortKey => $this->decode($val)];
        })->toArray();
    }

    public function set(string $key, mixed $value, ?int $userId = null, ...$args): ?Setting
    {
        $fullKey = $this->qualifyKey($key);

        $columns = [
            'user_id' => $userId,
            'module'  => $this->moduleSlug,
            'value'   => is_array($value) || is_object($value)
                ? json_encode($value)
                : $value,
        ];

        Cache::forget((new KonekoCacheManager($this->component, 'settings'))->key(md5($fullKey)));

        return Setting::updateOrCreate(['key' => $fullKey, 'user_id' => $userId], $columns);
    }

    public function delete(string $key): bool
    {
        $fullKey = $this->qualifyKey($key);
        Cache::forget((new KonekoCacheManager($this->component, 'settings'))->key(md5($fullKey)));
        return Setting::where('key', $fullKey)->delete() > 0;
    }

    public function deleteNamespace(string $namespace, bool $fireEvents = true): int
    {
        return Setting::where('key', 'like', rtrim($namespace, '.') . '.%')->delete();
    }

    public function listGroups(): SettingsGroupCollection
    {
        $keys = Setting::pluck('key')->toArray();
        return new SettingsGroupCollection(
            collect($keys)->map(fn($key) => explode('.', $key)[0])->unique()->values()
        );
    }

    public function currentNamespace(): string
    {
        return $this->namespace;
    }

    protected function qualifyKey(string $key): string
    {
        return $this->namespace . ltrim($key, '.');
    }

    protected function query(string $key): mixed
    {
        $row = Setting::where('key', $key)->first();
        return $this->decode($row?->value);
    }

    protected function decode(mixed $value): mixed
    {
        if (is_string($value) && Str::startsWith($value, ['[', '{'])) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }
}
