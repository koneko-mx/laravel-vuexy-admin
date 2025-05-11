<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\{Event, Schema};
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Events\Settings\SettingChanged;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

class KonekoSettingManager extends AbstractKeyValueCacheBuilder implements SettingsRepositoryInterface
{
    // Flags de control del setting
    protected bool $is_system    = false;
    protected bool $is_encrypted = false;
    protected bool $is_sensitive = false;
    protected bool $is_editable  = true;
    protected bool $is_active    = true;

    protected bool $trackUsage   = true;

    protected string $settingModel = Setting::class;

    // ========== Fluent Flag Setters ==========
    public function markAsSystem(bool $state = true): static { $this->is_system = $state; return $this; }
    public function markAsEncrypted(bool $state = true): static { $this->is_encrypted = $state; return $this; }
    public function markAsSensitive(bool $state = true): static { $this->is_sensitive = $state; return $this; }
    public function markAsEditable(bool $state = true): static { $this->is_editable = $state; return $this; }
    public function markAsActive(bool $state = true): static { $this->is_active = $state; return $this; }


    // ========== Usage Tracking Control ==========
    public function withoutUsageTracking(): static
    {
        $this->trackUsage = false;
        return $this;
    }

    // ========== Settings Operations ==========
    public function set(string $key, mixed $value): ?Setting
    {
        $this->validateKey($key);

        $fullKey = $this->generateCacheKey($key);

        $columns = [
            'namespace'    => $this->namespace,
            'environment'  => app()->environment(),
            'scope'        => $this->scope,
            'component'    => $this->component,
            'module'       => $this->module,
            'group'        => $this->group,
            'sub_group'    => $this->subGroup,
            'key_name'     => $key,
            'user_id'      => $this->currentUser()?->getAuthIdentifier() ?? null,
            'is_system'    => $this->is_system,
            'is_encrypted' => $this->is_encrypted,
            'is_sensitive' => $this->is_sensitive,
            'is_editable'  => $this->is_editable,
            'is_active'    => $this->is_active,
        ];

        $setting = $this->settingModel::updateOrCreate(
            ['key' => $fullKey],
            [...$columns, 'value' => $value]
        );


        Event::dispatch(new SettingChanged($fullKey));

        return $setting;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->rememberCache($key, fn() => $this->query($this->generateCacheKey($key)));

        return $value !== null ? $value : $default;
    }


    public function delete(string $key): bool
    {
        $fullKey = $this->generateCacheKey($key);

        $deleted = $this->settingModel::where('key', $fullKey)->delete();

        if ($deleted) {
            Event::dispatch(new SettingChanged($fullKey));
        }

        return $deleted > 0;
    }

    public function exists(string $key): bool
    {
        return $this->settingModel::where('key', $this->generateCacheKey($key))->exists();
    }

    /**
     * Obtiene un setting por su clave calificada.
     */
    protected function query(string $key): mixed
    {
        if (!Schema::hasTable((new $this->settingModel)->getTable())) return null;

        $setting = $this->settingModel::where('key', $key)->first();

        if ($setting && $this->trackUsage) {
            $setting->incrementUsage();
        }

        // Reiniciar flag para evitar que quede en false en la siguiente consulta
        $this->trackUsage = true;

        return $setting?->value;
    }
}
