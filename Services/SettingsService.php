<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Obtiene una configuración con opciones avanzadas de caché.
     *
     * @param string $key
     * @param int|null $userId
     * @param string|null $category
     * @param bool $useCache
     * @param bool $storeInCache
     * @param int|null $cacheTtl
     * @return mixed|null
     */
    public function get(
        string $key,
        ?int $userId = null,
        ?string $category = null,
        bool $useCache = false,
        bool $storeInCache = true,
        ?int $cacheTtl = 120
    ) {
        $cacheKey = $this->generateCacheKey($key, $userId, $category);

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $value = $this->retrieveSetting($key, $userId, $category);

        if ($storeInCache && $value !== null) {
            Cache::put($cacheKey, $value, now()->addMinutes($cacheTtl));
        }

        return $value;
    }

    /**
     * Guarda o actualiza una configuración con control de caché.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @param string|null $category
     * @param string|null $mimeType
     * @param string|null $fileName
     * @param bool $updateCache
     * @param int|null $cacheTtl
     * @return Setting|null
     */
    public function set(
        string $key,
        mixed $value,
        ?int $userId = null,
        ?string $category = null,
        ?string $mimeType = null,
        ?string $fileName = null,
        bool $updateCache = false,
        ?int $cacheTtl = 120
    ): ?Setting {
        $data = [
            'user_id'    => $userId,
            'category'   => $category,
            'mime_type'  => $mimeType,
            'file_name'  => $fileName,
            // Inicializar todos los campos de valor como null
            'value_string'  => null,
            'value_integer' => null,
            'value_boolean' => null,
            'value_float'   => null,
            'value_text'    => null,
            'value_binary'  => null,
        ];

        // Detectar tipo de valor
        if (is_string($value)) {
            // Evaluamos la longitud de la cadena
            $threshold = 250;

            if (strlen($value) > $threshold) {
                $data['value_text'] = $value;
            } else {
                $data['value_string'] = $value;
            }
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

        // Se registra usuario que realiza la acción
        if (Auth::check()) {
            $data['updated_by'] = Auth::id();
        }

        $setting = Setting::updateOrCreate(
            ['key' => $key, 'user_id' => $userId, 'category' => $category],
            $data
        );

        if ($updateCache) {
            $cacheKey = $this->generateCacheKey($key, $userId, $category);
            Cache::put($cacheKey, $setting->value, now()->addMinutes($cacheTtl));
        }

        return $setting;
    }

    /**
     * Elimina una configuración.
     *
     * @param string $key
     * @param int|null $userId
     * @param string|null $category
     * @return Setting|null La configuración eliminada o null si no existía
     */
    public function delete(string $key, ?int $userId = null, ?string $category = null): ?Setting
    {
        $setting = Setting::where('key', $key);

        if ($userId !== null) {
            $setting->where('user_id', $userId);
        }

        if ($category !== null) {
            $setting->where('category', $category);
        }

        $setting = $setting->first();

        if ($setting) {
            $cacheKey = $this->generateCacheKey($key, $userId, $category);
            Cache::forget($cacheKey);
            $setting->delete();
        }

        return $setting;
    }

    /**
     * Recupera una configuración de la base de datos.
     *
     * @param string $key
     * @param integer|null $userId
     * @param string|null $category
     * @return void
     */
    protected function retrieveSetting(string $key, ?int $userId, ?string $category)
    {
        $query = Setting::where('key', $key);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($category !== null) {
            $query->where('category', $category);
        }

        return $query->first()?->value;
    }

    /**
     * Genera una clave de caché para una configuración.
     *
     * @param string $key
     * @param integer|null $userId
     * @param string|null $category
     * @return string
     */
    protected function generateCacheKey(string $key, ?int $userId, ?string $category): string
    {
        return 'settings:' . md5($key . '|' . $userId . '|' . $category);
    }
}
