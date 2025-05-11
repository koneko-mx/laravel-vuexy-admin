<?php

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\{Event, Schema};
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Events\Settings\SettingChanged;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

class KonekoSettingManager extends AbstractKeyValueCacheBuilder implements SettingsRepositoryInterface
{
    protected bool $is_system    = false;
    protected bool $is_encrypted = false;
    protected bool $is_sensitive = false;
    protected bool $is_editable  = true;
    protected bool $is_active    = true;

    // Module name
    protected string $settingModel = Setting::class;

    public function __construct($component, $group, $subGroup, $scope)
    {
        $this->setContext($component, $group, $subGroup, $scope);
    }

    /**
     * Establece un setting.
     */
    public function set(string $key, mixed $value, ?int $userId = null, ...$args): ?Setting
    {
        if (!preg_match('/^[a-z0-9\.\-_]+$/', $key)) {
            throw new \InvalidArgumentException("La clave '{$key}' no es válida. Solo se permiten caracteres alfanuméricos, '.', '-', y '_'.");
        }

        $fullKey = $this->qualifyKey($key);

        $columns = [
            'namespace' => $this->namespace,
            'module'  => $this->module,
            'user_id' => $userId,
            'value_string' => null,
            'value_integer' => null,
            'value_boolean' => null,
            'value_float' => null,
            'value_text' => null,
            'value_binary' => null,
        ];

        if (is_string($value)) {
            $columns[strlen($value) > 250 ? 'value_text' : 'value_string'] = $value;

        } elseif (is_int($value)) {
            $columns['value_integer'] = $value;

        } elseif (is_bool($value)) {
            $columns['value_boolean'] = $value;

        } elseif (is_float($value)) {
            $columns['value_float'] = $value;

        } elseif (is_array($value) || is_object($value)) {
            $columns['value_text'] = is_string($value)
                ? $value
                : json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $setting = Setting::updateOrCreate(
            ['key' => $fullKey, 'user_id' => $userId],
            $columns
        );

        Event::dispatch(new SettingChanged(
            key: $fullKey,
            namespace: $this->namespace,
            userId: $userId
        ));

        return $setting;
    }

    /**
     * Obtiene un setting por su clave calificada.
     */
    public function get(string $key, ...$args): mixed
    {
        $fullKey = $this->qualifyKey($key);

        return $this->rememberCache($key, fn () => $this->query($fullKey));
    }

    /**
     * Obtiene todos los settings de un componente.
     */
    public function getScoped(string $component, ?string $group = null, ?int $userId = null): array {
        $query = $this->settingModel::query()
            ->where('namespace', $this->namespace)
            ->where('component', $component);

        if (!is_null($group)) {
            $query->where('group', $group);
        }

        if (!is_null($userId)) {
            $query->where('user_id', $userId);
        }

        return $query->get()->toArray();
    }

    /**
     * Obtiene todos los settings de un componente.
     */
    public function getComponent(string $component, ?int $userId = null): array
    {
        return $this->getScoped($component, null, $userId);
    }

    /**
     * Obtiene todos los settings de un grupo.
     */
    public function getGroup(string $group, ?int $userId = null): array
    {
        return $this->getScoped($this->component, $group, $userId);
    }

    /**
     * Elimina un setting por su clave calificada.
     */
    public function delete(string $key, ?int $userId = null): bool
    {
        $fullKey = $this->qualifyKey($key);
        $deleted = $this->settingModel::where('key', $fullKey)->delete();

        if ($deleted) {
            Event::dispatch(new SettingChanged(
                key: $fullKey,
                namespace: $this->namespace,
                userId: $userId
            ));
        }

        return $deleted > 0;
    }

    /**
     * Elimina todos los settings de un componente.
     */
    public function deleteScoped(string $component, ?string $group = null, ?int $userId = null): int {
        $query = $this->settingModel::query()
            ->where('namespace', $this->namespace)
            ->where('component', $component);

        if (!is_null($group)) {
            $query->where('group', $group);
        }

        if (!is_null($userId)) {
            $query->where('user_id', $userId);
        }

        return $query->delete();
    }

    /**
     * Elimina todos los settings de un componente.
     */
    public function deleteComponent(string $component, ?int $userId = null): int
    {
        return $this->deleteScoped(
            component: $component,
            group: null,
            userId: $userId
        );
    }

    /**
     * Elimina todos los settings de un grupo.
     */
    public function deleteGroup(string $group, ?int $userId = null): int
    {
        if (empty($this->component)) {
            throw new \InvalidArgumentException("El componente es obligatorio.");
        }

        return $this->deleteScoped(
            component: $this->component,
            group: $group,
            userId: $userId
        );
    }

    /**
     * Obtiene todos los componentes.
     */
    public function listComponents(): array
    {
        return Setting::select('component')->distinct()->get()->pluck('component')->toArray();
    }

    /**
     * Obtiene todos los grupos.
     */
    public function listGroups(): array
    {
        if (empty($this->component)) {
            throw new \InvalidArgumentException("El componente es obligatorio.");
        }

        return Setting::select('group')->where('component', $this->component)->distinct()->get()->pluck('group')->toArray();
    }










    public function markAsSystem(bool $state = true): static
    {
        $this->is_system = $state;
        return $this;
    }
    
    public function markAsEncrypted(bool $state = true): static
    {
        $this->is_encrypted = $state;
        return $this;
    }
    
    public function markAsSensitive(bool $state = true): static
    {
        $this->is_sensitive = $state;
        return $this;
    }

    public function markAsEditable(bool $state = true): static
    {
        $this->is_editable = $state;
        return $this;
    }

    public function markAsActive(bool $state = true): static
    {
        $this->is_active = $state;
        return $this;
    }













    /**
     * Verifica si un setting existe.
     */
    public function exists(string $key): bool
    {
        return $this->settingModel::where('key', $this->qualifyKey($key))->exists();
    }

    /**
     * Genera una clave calificada para un setting.
     */
    protected function qualifyKey(string $key): string
    {
        $this->validateContext();

        return "{$this->namespace}.{$this->component}.{$this->group}.{$key}";
    }

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
}
