<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\Events\Settings\SettingChanged;
use Koneko\VuexyAdmin\Application\Settings\SettingsGroupCollection;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;

/**
 * Servicio principal para gestionar configuraciones del sistema y módulos.
 */
class ___SettingsService implements SettingsRepositoryInterface
{
    use InteractsWithKonekoVarsCache;

    private const CACHE_PREFIX = 'settings_user_id:';

    /**
     * Namespace base del módulo (ej. koneko.admin).
     */
    private string $defaultNamespace = '';

    /**
     * Namespace activo para operaciones dinámicas.
     */
    private ?string $currentNamespace = null;

    /**
     * Slug del módulo actual para auto-llenar campo 'module' en tabla settings.
     */
    private ?string $currentModuleSlug = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initCacheConfig();

        $this->defaultNamespace  = '';
        $this->currentNamespace  = null;
        $this->currentModuleSlug = null;
    }

    /**
     * Define el namespace base y slug del módulo actual.
     *
     * @param string $namespace
     * @param string|null $slug
     * @return self
     */
    public function setDefaultNamespace(string $namespace, ?string $slug = null): self
    {
        $this->defaultNamespace = rtrim($namespace, '.') . '.';
        $this->currentNamespace = $this->defaultNamespace;
        $this->currentModuleSlug = $slug;

        return $this;
    }

    /**
     * Usa el namespace propio del módulo actual, con posibilidad de extenderlo.
     *
     * @param string|null $subNamespace
     * @return self
     */
    public function self(?string $subNamespace = null): self
    {
        $namespace = $this->defaultNamespace;

        if ($subNamespace !== null) {
            $namespace .= rtrim($subNamespace, '.') . '.';
        }

        $this->currentNamespace = $namespace;

        return $this;
    }

    /**
     * Cambia temporalmente a otro namespace.
     *
     * @param string $namespace
     * @return self
     */
    public function in(string $namespace): self
    {
        $this->currentNamespace = rtrim($namespace, '.') . '.';
        return $this;
    }

    /**
     * Obtiene un valor de configuración.
     *
     * @param string $key
     * @param mixed ...$args
     * @return mixed
     */
    public function get(string $key, ...$args): mixed
    {
        return $this->retrieveSetting($this->qualifyKey($key));
    }

    /**
     * Obtiene un conjunto de settings basado en el namespace actual.
     *
     * @param string|null $prefix Prefijo adicional dentro del namespace (puede ser vacío '')
     * @return array
     */
    public function getGroup(?string $prefix = ''): array
    {
        $namespace = rtrim($this->currentNamespace ?? $this->defaultNamespace, '.');

        $query = Setting::where('key', 'like', "{$namespace}.%");

        if ($prefix !== '') {
            $prefix = trim($prefix, '.');
            $query->where('key', 'like', "{$namespace}.{$prefix}.%");
        }

        $settings = $query->pluck('value', 'key')->toArray();

        // Limpiar claves: quitar el namespace
        $cleaned = [];

        foreach ($settings as $fullKey => $value) {
            $shortKey = (string) str($fullKey)->after("{$namespace}.");
            $cleaned[$shortKey] = $value;
        }

        return $cleaned;
    }

    /**
     * Guarda o actualiza un valor de configuración.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @param mixed ...$args
     * @return Setting|null
     */
    public function set(string $key, mixed $value, ?int $userId = null, ...$args): ?Setting
    {
        $qualifiedKey = $this->qualifyKey($key);

        $data = [
            'user_id'        => $userId,
            'module'         => $this->currentModuleSlug,
            'value_string'   => null,
            'value_integer'  => null,
            'value_boolean'  => null,
            'value_float'    => null,
            'value_text'     => null,
            'value_binary'   => null,
        ];

        if (is_string($value)) {
            $data[strlen($value) > 250 ? 'value_text' : 'value_string'] = $value;
        } elseif (is_int($value)) {
            $data['value_integer'] = $value;
        } elseif (is_bool($value)) {
            $data['value_boolean'] = $value;
        } elseif (is_float($value)) {
            $data['value_float'] = $value;
        } elseif (is_resource($value) || $value instanceof \SplFileInfo) {
            $data['value_binary'] = is_resource($value)
                ? stream_get_contents($value)
                : file_get_contents($value->getRealPath());
        } elseif (is_array($value) || is_object($value)) {
            $data['value_text'] = json_encode($value);
        }

        $setting = Setting::updateOrCreate(
            ['key' => $qualifiedKey, 'user_id' => $userId],
            $data
        );

        Event::dispatch(new SettingChanged($qualifiedKey));

        return $setting;
    }

    /**
     * Lista todos los grupos disponibles.
     *
     * @return SettingsGroupCollection
     */
    public function listGroups(): SettingsGroupCollection
    {
        $allSettings = Setting::pluck('key')->toArray();

        $groups = collect($allSettings)
            ->map(fn($key) => explode('.', $key)[0] ?? 'unknown')
            ->unique()
            ->values();

        if ($this->currentNamespace !== null) {
            $namespaceRoot = rtrim($this->currentNamespace, '.');
            $groups = $groups->filter(fn($group) => $group === $namespaceRoot);
        }

        return new SettingsGroupCollection($groups);
    }

    /**
     * Devuelve el namespace actualmente activo.
     *
     * @return string
     */
    public function currentNamespace(): string
    {
        return $this->currentNamespace ?? $this->defaultNamespace;
    }

    /**
     * Internamente califica la key con el namespace activo.
     *
     * @param string $key
     * @return string
     */
    private function qualifyKey(string $key): string
    {
        return $this->currentNamespace . $key;
    }

    /**
     * Recupera un setting directamente de la base de datos.
     *
     * @param string $key
     * @return mixed
     */
    private function retrieveSetting(string $key): mixed
    {
        $cacheKey = $this->generateCacheKey($key);

        return $this->cacheOrCompute($cacheKey, fn () => $this->querySettingFromDatabase($key));
    }

    /**
     * Consulta un setting directamente desde la base de datos.
     *
     * @param string $key
     * @return mixed
     */
    private function querySettingFromDatabase(string $key): mixed
    {
        // Evita error si la tabla aún no existe (por ejemplo durante migrate:fresh)
        if (!Schema::hasTable('settings')) {
            return null;
        }

        $setting = Setting::where('key', $key)->first();
        $value = $setting?->value;

        if (is_string($value) && $this->isJson($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $value;
    }


    /**
     * Verifica si un valor string es JSON.
     *
     * @param string $value
     * @return bool
     */
    private function isJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function delete(string $key): bool
    {
        $qualifiedKey = $this->qualifyKey($key);

        $deleted = Setting::where('key', $qualifiedKey)->delete();

        if ($deleted) {
            Event::dispatch(new SettingChanged($qualifiedKey));
        }

        return $deleted > 0;
    }

    public function deleteNamespace(string $namespace, bool $fireEvents = true): int
    {
        $qualifiedNamespace = rtrim($namespace, '.') . '.';

        $settings = Setting::where('key', 'like', "{$qualifiedNamespace}%")->get();

        $deletedCount = 0;

        foreach ($settings as $setting) {
            $setting->delete();
            $deletedCount++;

            if ($fireEvents) {
                Event::dispatch(new SettingChanged($setting->key));
            }
        }

        return $deletedCount;
    }

    /**
     * Genera una cache key para un setting.
     *
     * @param string $key
     * @return string
     */
    private function generateCacheKey(string $key): string
    {
        return self::CACHE_PREFIX . md5($key);
    }
}
