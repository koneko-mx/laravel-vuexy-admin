<?php

namespace Koneko\VuexyAdmin\Application\Settings\Manager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Application\Cache\Builders\SettingCacheKeyBuilder;
use Koneko\VuexyAdmin\Application\Cache\Driver\KonekoCacheDriver;
use Koneko\VuexyAdmin\Application\Cache\Contracts\CacheRepositoryInterface;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Settings\Concerns\{HasSettingAttributes, HasSettingCache, HasSettingEncryption, HasSettingFileSupport, HasSettingMetadata};
use Koneko\VuexyAdmin\Application\Settings\SettingDefaults;
use Koneko\VuexyAdmin\Application\Traits\System\Context\{HasBaseContext, HasContextQueryBuilder, HasSettingsContextValidation};
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

    private bool $includeDisabled = false;
    private bool $includeExpired  = false;
    private bool $asArray       = false;
    protected bool $bypassCache = false;

    protected string $settingModel = Setting::class;

    public function __construct()
    {
        $this->setNamespace()
            ->environment()
            ->loadModuleClass(CoreModule::class)
            ->group(SettingDefaults::DEFAULT_GROUP)
            ->section(SettingDefaults::DEFAULT_SECTION)
            ->subGroup(SettingDefaults::DEFAULT_SUB_GROUP);
    }

    // ==================== Factory ====================

    public static function make(): static
    {
        return new static();
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
        if ($keyName) {
            $this->keyName($keyName);
        }

        $this->validateContextWithScope();

        $qualifiedKey = $this->getQualifiedKey();

        /** @var Setting $setting */
        $setting = $this->settingModel::updateOrCreate(
            ['key' => $qualifiedKey],
            array_merge(
                $this->context,
                $this->attributes,
                $this->file,
                $this->encryption,
                $this->cache,
                $this->metadata
            )
        );

        $setting->value = $value;
        $setting->save();

        $this->cacheModel($setting);

        $this->reset();
    }

    public function groupSettings(array $data): void
    {
        $this->validateContextWithScope();

        foreach ($data as $key => $value) {
            $this->keyName($key);
            $qualifiedKey = $this->getQualifiedKey();

            /** @var Setting $setting */
            $setting = $this->settingModel::updateOrCreate(
                ['key' => $qualifiedKey],
                array_merge(
                    $this->context,
                    $this->attributes,
                    $this->file,
                    $this->encryption,
                    $this->cache,
                    $this->metadata
                )
            );

            $setting->value = $value;
            $setting->save();

            $this->cacheModel($setting);
        }

        $this->reset();
    }

    public function get(?string $keyName = null, mixed $default = null): mixed
    {
        if ($this->isTableNotExists()) return $default;

        if ($keyName) {
            $this->keyName($keyName);
        }

        $this->validateContextWithScope();

        $manager = $this->getCacheManager();

        if (!$manager->isEnabled() || $this->bypassCache) {
            return $this->queryByKey()->first()?->value ?? $default;
        }

        $key = $manager->getQualifiedKey();
        $cached = KonekoCacheDriver::get($key);

        if (!is_null($cached)) {
            return $cached;
        }

        $model = $this->queryByKey()->first();

        if (!$model) {
            $manager->forget();
            return $default;
        }

        $this->cacheModel($model);

        return $model->value;
    }

    public function exists(string $key): bool
    {
        return SettingCacheKeyBuilder::isQualified($key)
            ? $this->settingModel::query()->where('key', $key)->exists()
            : $this->queryByKey()->exists();
    }

    public function existsByContext(): bool
    {
        $this->validateContextWithScope();

        return $this->query()->exists();
    }

    public function delete(string $qualifiedKey): void
    {
        $this->settingModel::where('key', $qualifiedKey)->delete();
        $this->getCacheManager()->keyName($qualifiedKey)->forget();
    }

    public function all(): Collection|array
    {
        // Shortcut si la tabla no existe
        if ($this->isTableNotExists()) {
            return $this->asArray ? [] : collect();
        }

        $query = $this->settingModel::query();

        // Filtra usando el contexto actual
        foreach ($this->context as $field => $value) {
            if (!is_null($value)) {
                $query->where($field, $value);
            }
        }

        // Siempre filtra activos, a menos que se indique lo contrario
        if (!$this->includeDisabled) {
            $query->where('is_active', true);
        }

        if (!$this->includeExpired) {
            $query->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        }

        // Devuelve como array clave/valor si así se pidió
        $result = $query->get();

        if ($this->asArray) {
            return $result->mapWithKeys(fn($s) => [$s->key_name => $s->value])->toArray();
        }

        return $result;
    }

    public function deleteByContext(): int
    {
        $this->validateContextWithScope();

        return $this->query()
            ->tap(fn($q) => $q->each(fn($setting) => $this->getCacheManager()->keyName($setting->key_name)->forget()))
            ->delete()
            ->count();
    }

    public function deleteGroup(): int
    {
        return $this->queryByGroup($this->newQuery(), $this->context)
            ->tap(fn($q) => $q->each(fn($setting) => $this->getCacheManager()->keyName($setting->key_name)->forget()))
            ->delete()
            ->count();
    }

    public function deleteSubGroup(): int
    {
        return $this->queryBySubGroup($this->newQuery(), $this->context)
            ->tap(fn($q) => $q->each(fn($setting) => $this->getCacheManager()->keyName($setting->key_name)->forget()))
            ->delete()
            ->count();
    }

    // ================= Fetchers =================

    public function getGroup(bool $asArray = false): Collection|array
    {
        $query = $this->queryByGroup($this->newQuery(), $this->context)->get();
        return $asArray || $this->asArray ? $query->pluck('group')->toArray() : $query;
    }

    public function getSubGroup(bool $asArray = false): Collection|array
    {
        if ($this->isTableNotExists()) return [];

        $query = $this->queryBySubGroup($this->newQuery(), $this->context)->get();
        return $asArray || $this->asArray ? $query->pluck('sub_group')->toArray() : $query;
    }

    public function getComponents(bool $asArray = false): Collection|array
    {
        $query = $this->newQuery()
            ->select('component')
            ->distinct()
            ->get();

        return $asArray || $this->asArray ? $query->pluck('component')->toArray() : $query;
    }

    public function getGroups(bool $asArray = false): Collection|array
    {
        $query = $this->newQuery()
            ->select('group')
            ->distinct()
            ->get();

        return $asArray || $this->asArray ? $query->pluck('group')->toArray() : $query;
    }

    public function getSubGroups(bool $asArray = false): Collection|array
    {
        $query = $this->newQuery()
            ->select('sub_group')
            ->distinct()
            ->get();

        return $asArray || $this->asArray ? $query->pluck('sub_group')->toArray() : $query;
    }

    // ======================= HELPERS =========================

    public function queryForModel(): ?Model
    {
        return $this->queryByKey()->first();
    }

    public function getCacheManager(): CacheRepositoryInterface
    {
        return cache_m()->setContextArray($this->context);
    }

    public function isUsable(): bool
    {
        return $this->queryByKey()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function setInactiveByContext(): int
    {
        return $this->query()->update([
            'is_active'  => false,
            'expires_at' => now(),
        ]);
    }


    public function has(string $qualifiedKey): bool
    {
        return $this->queryByKey()->where('key', $qualifiedKey)->exists();
    }

    public function hasContext(): bool
    {
        return $this->hasBaseContext()
            && $this->hasGroupContext();
    }

    public function defaults(): static
    {
        $this->reset();
        return $this;
    }

    public function reset(): void
    {
        $this->includeDisabled = false;
        $this->includeExpired  = false;
        $this->asArray       = false;
        $this->bypassCache   = false;

        $this->context['group']     = SettingDefaults::DEFAULT_GROUP;
        $this->context['section']   = SettingDefaults::DEFAULT_SECTION;
        $this->context['sub_group'] = SettingDefaults::DEFAULT_SUB_GROUP;

        $this->attributes = [
            'is_system'       => false,
            'is_sensitive'    => false,
            'is_file'         => false,
            'is_encrypted'    => false,
            'is_editable'     => true,
            'is_track_usage'  => SettingDefaults::DEFAULT_TRACK_USAGE,
            'is_should_cache' => SettingDefaults::DEFAULT_SHOULD_CACHE,
            'is_active'       => true,
            'expires_at'      => null,
        ];

        $this->file = [
            'mime_type' => null,
            'file_name' => null,
        ];

        $this->encryption = [
            'encryption_algorithm'  => SettingDefaults::DEFAULT_ALGORITHM,
            'encryption_key'        => null,
            'encryption_rotated_at' => null,
        ];

        $this->cache = [
            'cache_ttl'        => null,
            'cache_expires_at' => null,
        ];

        $this->metadata = [
            'description' => null,
            'hint'        => null,
        ];
    }

    public function info(): array
    {
        return [
            'context' => $this->context,
            'attributes' => $this->attributes,
            'file' => $this->file,
            'encryption' => $this->encryption,
            'cache' => $this->cache,
            'metadata' => $this->metadata,
        ];
    }

    // ======================= PROTECTED =========================

    protected function isTableNotExists(): bool
    {
        return !Schema::hasTable((new $this->settingModel)->getTable());
    }
}
