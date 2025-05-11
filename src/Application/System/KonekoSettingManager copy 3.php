<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Settings;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Application\Enums\Settings\SettingValueType;
use Koneko\VuexyAdmin\Application\Events\Settings\SettingChanged;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

class KonekoSettingManager extends AbstractKeyValueCacheBuilder// implements SettingsRepositoryInterface
{
    // Flags de control del setting
    protected bool $is_system    = false;
    protected bool $is_encrypted = false;
    protected bool $is_sensitive = false;
    protected bool $is_editable  = true;
    protected bool $is_active    = true;

    protected string $settingModel = Setting::class;

    // ========== Fluent Flag Setters ==========
    public function markAsSystem(bool $state = true): static { $this->is_system = $state; return $this; }
    public function markAsEncrypted(bool $state = true): static { $this->is_encrypted = $state; return $this; }
    public function markAsSensitive(bool $state = true): static { $this->is_sensitive = $state; return $this; }
    public function markAsEditable(bool $state = true): static { $this->is_editable = $state; return $this; }
    public function markAsActive(bool $state = true): static { $this->is_active = $state; return $this; }

    // ========== Settings Operations ==========
    public function set(string $key, mixed $value): ?Setting
    {
        $this->validateKey($key);

            //$this->user =

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
            'user_id'      => $userId,
            'is_system'    => $this->is_system,
            'is_encrypted' => $this->is_encrypted,
            'is_sensitive' => $this->is_sensitive,
            'is_editable'  => $this->is_editable,
            'is_active'    => $this->is_active,
        ];

        $this->assignValueColumns($columns, $value);

        $setting = Setting::updateOrCreate(
            ['key' => $fullKey],
            $columns
        );

        Event::dispatch(new SettingChanged($fullKey, $this->namespace, $userId));

        return $setting;
    }

    public function get(string $key): mixed
    {
        $fullKey = $this->generateCacheKey($key);

        return $this->rememberCache($key, fn() => $this->query($fullKey));
    }

    public function delete(string $key): bool
    {
        $fullKey = $this->generateCacheKey($key);
        $deleted = Setting::where('key', $fullKey)->delete();

        if ($deleted) {
            Event::dispatch(new SettingChanged($fullKey));
        }

        return $deleted > 0;
    }

    public function exists(string $key): bool
    {
        return Setting::where('key', $this->generateCacheKey($key))->exists();
    }

    // ===================== BÚSQUEDA AVANZADA =====================
        public function getScoped(string $scope, ?int $userId = null): array
        {
            return Setting::where('scope', $scope)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->get()->toArray();
        }

        public function getComponent(string $component, ?int $userId = null): array
        {
            return Setting::where('component', $component)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->get()->toArray();
        }

        public function getGroup(string $group, ?int $userId = null): array
        {
            return Setting::where('group', $group)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->get()->toArray();
        }

        public function getSubGroup(string $subGroup, ?int $userId = null): array
        {
            return Setting::where('sub_group', $subGroup)
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->get()->toArray();
        }

    // ===================== ELIMINACIÓN =====================
        public function deleteScoped(string $scope): int
        {
            return Setting::where('scope', $scope)->delete();
        }

        public function deleteComponent(string $component): int
        {
            return Setting::where('component', $component)->delete();
        }

        public function deleteGroup(string $group): int
        {
            return Setting::where('group', $group)->delete();
        }

        public function deleteSubGroup(string $subGroup): int
        {
            return Setting::where('sub_group', $subGroup)->delete();
        }

    // ===================== LISTADOS =====================
        public function listComponents(): array
        {
            return Setting::select('component')->distinct()->pluck('component')->toArray();
        }

        public function listGroups(): array
        {
            return Setting::select('group')->distinct()->pluck('group')->toArray();
        }

        public function listSubGroups(): array
        {
            return Setting::select('sub_group')->distinct()->pluck('sub_group')->toArray();
        }

    // ===================== Internal Helpers =====================

    /**
     * Obtiene un setting por su clave calificada.
     */
    protected function query(string $key): mixed
    {
        if (!Schema::hasTable('settings')) return null;

        $setting = $this->settingModel::where('key', $key)->first();

        return $setting ? $this->decode($setting) : null;
    }

    /**
     * Decodifica el valor de un setting.
     */
    protected function decode(Setting $setting, bool $asArray = true): mixed
    {
        // Orden de prioridad: JSON largo, texto simple, luego tipos básicos
        $value = $setting->value_text
            ?? $setting->value_string
            ?? $setting->value_integer
            ?? $setting->value_boolean
            ?? $setting->value_float
            ?? $setting->value_binary
            ?? null;

        if (is_string($value)) {
            $value = trim($value);

            // Limpieza de caracteres invisibles si es string
            $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);

            // Intentar decodificar si parece JSON
            if (Str::startsWith($value, ['[', '{'])) {
                $decoded = json_decode($value, $asArray);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }

                logger()->warning('⚠️ JSON decode failed', [
                    'key' => $setting->key,
                    'value_preview' => Str::limit($value, 200),
                    'error' => json_last_error_msg(),
                ]);
            }
        }

        return $value;
    }

    protected function assignValueColumns(array &$columns, mixed $value): void
    {
        foreach (SettingValueType::cases() as $type) {
            $columns["value_{$type->value}"] = null;
        }

        match (true) {
            is_string($value) => $columns[strlen($value) > 250 ? 'value_text' : 'value_string'] = $value,
            is_int($value)   => $columns['value_integer'] = $value,
            is_bool($value)  => $columns['value_boolean'] = $value,
            is_float($value) => $columns['value_float'] = $value,
            is_array($value), is_object($value) => $columns['value_text'] = json_encode($value, JSON_UNESCAPED_UNICODE),
            default => null
        };
    }






}