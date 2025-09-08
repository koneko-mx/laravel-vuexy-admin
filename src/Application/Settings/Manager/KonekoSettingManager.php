<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Settings\Manager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Application\Cache\Contracts\CacheRepositoryInterface;
use Koneko\VuexyAdmin\Application\Cache\Driver\KonekoCacheDriver;
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Settings\Concerns\{
    HasSettingAttributes,
    HasSettingCache,
    HasSettingEncryption,
    HasSettingFileSupport,
    HasSettingMetadata
};
use Koneko\VuexyAdmin\Application\Traits\System\Context\{
    HasBaseContext,
    HasContextQueryBuilder,
    HasSettingsContextValidation
};
use Koneko\VuexyAdmin\Models\Setting;

final class KonekoSettingManager implements SettingsRepositoryInterface
{
    use HasBaseContext;
    use HasSettingAttributes;
    use HasSettingEncryption;
    use HasSettingFileSupport;
    use HasSettingMetadata;
    use HasSettingCache;
    use HasContextQueryBuilder;
    use HasSettingsContextValidation;

    /** Filtros globales de consulta */
    private bool $includeDisabled = false;
    private bool $includeExpired  = false;

    /** Formato de retorno */
    protected bool $asArray = false;

    /** Bypass de caché (solo lectura) */
    protected bool $bypassCache = false;

    /** Modelo backing */
    protected string $settingModel = Setting::class;

    public function __construct()
    {
        $namespace = config('koneko.namespace', 'koneko');
        $this->namespace($namespace)->environment();
    }

    // ==================== Factory ====================

    public static function make(array $context = []): static
    {
        $instance = new static();
        if ($context) {
            $instance->setContextArray($context);
        }
        return $instance;
    }

    // ==================== Context ====================

    public function includeDisabled(bool $state = true): static
    {
        $this->includeDisabled = $state;
        return $this;
    }

    public function includeExpired(bool $state = true): static
    {
        $this->includeExpired = $state;
        return $this;
    }

    public function bypassCache(bool $state = true): static
    {
        $this->bypassCache = $state;
        return $this;
    }

    public function asArray(bool $state = true): static
    {
        $this->asArray = $state;
        return $this;
    }

    // ==================== CRUD ====================

    public function set(string $keyName, mixed $value): void
    {
        $this->keyName($keyName);
        $this->validateContextWithScope();

        // Validaciones de flags dependientes
        $this->validateEncryption();
        $this->validateFile();

        $qualifiedKey = $this->getQualifiedKey();

        /** @var Setting $setting */
        $setting = $this->settingModel::updateOrCreate(
            ['key' => $qualifiedKey],
            array_filter(array_merge(
                $this->context,
                $this->attributes,
                $this->file,
                $this->encryption,
                $this->cache,
                $this->metadata
            ), static fn($v) => $v !== null)
        );

        $setting->value = $value;
        $setting->save();

        $this->cacheModel($setting);
    }

    public function setMany(array $kv): int
    {
        $this->validateContextWithScope();

        $n = 0;
        foreach ($kv as $keyName => $value) {
            $this->set((string) $keyName, $value);
            $n++;
        }
        return $n;
    }

    public function get(?string $keyName = null, mixed $default = null): mixed
    {
        if ($this->isTableNotExists()) {
            return $default;
        }

        if ($keyName) {
            $this->keyName($keyName);
        }

        $this->validateContextWithScope();

        $cache = $this->getCacheManager();

        // sin cache (deshabilitado o bypass)
        if (!$cache->isEnabled() || $this->bypassCache) {
            return $this->queryByKey()->first()?->value ?? $default;
        }

        // cache
        $qualifiedCacheKey = $cache->getQualifiedKey();
        $cached = KonekoCacheDriver::get($qualifiedCacheKey);

        if (!is_null($cached)) {
            return $cached;
        }

        $model = $this->queryByKey()->first();
        if (!$model) {
            $cache->forget(); // invalidación defensiva
            return $default;
        }

        $this->cacheModel($model);
        return $model->value;
    }

    public function getMany(array $keyNames, bool $decrypt = false): array
    {
        $out = [];
        foreach ($keyNames as $name) {
            $this->keyName((string) $name);
            $out[$name] = $decrypt
                ? $this->queryByKey()->first()?->getDecryptedValue()
                : $this->get(null);
        }
        return $out;
    }

    public function all(): Collection|array
    {
        if ($this->isTableNotExists()) {
            return $this->asArray ? [] : collect();
        }

        $result = $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
            'section'     => true,
            'sub_group'   => true,
        ])->get();

        return $this->asArray
            ? $result->mapWithKeys(fn(Setting $s) => [$s->key_name => $s->value])->toArray()
            : $result;
    }

    // ==================== Deleters ====================

    public function deleteByKeyName(?string $keyName = null): int
    {
        if ($keyName) {
            $this->keyName($keyName);
        }
        $this->validateContextWithScope();

        // invalidar cache
        $this->getCacheManager()->forget();

        // borrar DB por clave calificada
        return $this->deleteByQualifiedKey($this->getQualifiedKey());
    }

    public function deleteByQualifiedKey(string $qualifiedKey): int
    {
        // invalidar cache por key calificada (sin prefijo)
        KonekoCacheDriver::forget($qualifiedKey);

        return $this->settingModel::query()
            ->where('key', $qualifiedKey)
            ->delete();
    }

    public function deleteByContext(): int
    {
        return $this->withAllStates(function () {
            $keys = $this->query()->pluck('key_name')->all();
            foreach ($keys as $kn) {
                $this->getCacheManager()->keyName($kn)->forget();
            }
            return $this->query()->delete();
        });
    }

    public function deleteGroup(): int
    {
        return $this->withAllStates(function () {
            $keys = $this->queryByGroup()->pluck('key_name')->all();
            foreach ($keys as $kn) {
                $this->getCacheManager()->keyName($kn)->forget();
            }
            return $this->queryByGroup()->delete();
        });
    }

    public function deleteSubGroup(): int
    {
        return $this->withAllStates(function () {
            $keys = $this->queryBySubGroup()->pluck('key_name')->all();
            foreach ($keys as $kn) {
                $this->getCacheManager()->keyName($kn)->forget();
            }
            return $this->queryBySubGroup()->delete();
        });
    }

    public function deleteComponent(): int
    {
        return $this->withAllStates(function () {
            $keys = $this->queryByComponent()->pluck('key_name')->all();
            foreach ($keys as $kn) {
                $this->getCacheManager()->keyName($kn)->forget();
            }
            return $this->queryByComponent()->delete();
        });
    }

    // ==================== Utils ====================

    public function hasKeyName(?string $keyName = null): bool
    {
        return ($keyName !== null && $keyName !== '')
            || (!empty($this->context['key_name']));
    }

    public function hasQualifiedKey(?string $qualifiedKey = null): bool
    {
        if ($qualifiedKey === null) {
            if (!$this->hasKeyName()) {
                return false;
            }
            $qualifiedKey = $this->getQualifiedKey();
        }

        return $this->settingModel::query()
            ->where('key', $qualifiedKey)
            ->exists();
    }

    public function info(): array
    {
        return [
            'context'    => $this->context,
            'attributes' => $this->attributes,
            'file'       => $this->file,
            'encryption' => $this->encryption,
            'cache'      => $this->cache,
            'metadata'   => $this->metadata,
            'flags'      => [
                'includeDisabled' => $this->includeDisabled,
                'includeExpired'  => $this->includeExpired,
                'asArray'         => $this->asArray,
                'bypassCache'     => $this->bypassCache,
            ],
        ];
    }

    // ==================== Helpers internos ====================

    public function queryForModel(): ?Model
    {
        return $this->queryByKey()->first();
    }

    public function getCacheManager(): CacheRepositoryInterface
    {
        return KonekoCacheManager::make($this->context);
    }

    protected function isTableNotExists(): bool
    {
        return !Schema::hasTable((new $this->settingModel)->getTable());
    }

    /**
     * Ejecuta una operación ignorando filtros de activo/expirado.
     *
     * @template T
     * @param  callable():T  $fn
     * @return T
     */
    private function withAllStates(callable $fn)
    {
        $prevDisabled = $this->includeDisabled;
        $prevExpired  = $this->includeExpired;

        $this->includeDisabled = true;
        $this->includeExpired  = true;

        try {
            return $fn();
        } finally {
            $this->includeDisabled = $prevDisabled;
            $this->includeExpired  = $prevExpired;
        }
    }
}
