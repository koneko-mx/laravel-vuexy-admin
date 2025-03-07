<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Models\Setting;

class AdminTemplateService
{
    protected $cacheTTL = 60 * 24 * 30; // 30 días en minutos

    public function updateSetting(string $key, string $value): bool
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => trim($value)]
        );

        return $setting->save();
    }

    public function getAdminVars($adminSetting = false): array
    {
        try {
            // Verificar si el sistema está inicializado (la tabla `migrations` existe)
            if (!Schema::hasTable('migrations')) {
                return $this->getDefaultAdminVars($adminSetting);
            }

            // Cargar desde el caché o la base de datos si está disponible
            return Cache::remember('admin_settings', $this->cacheTTL, function () use ($adminSetting) {
                $settings = Setting::global()
                    ->where('key', 'LIKE', 'admin_%')
                    ->pluck('value', 'key')
                    ->toArray();

                $adminSettings = $this->buildAdminVarsArray($settings);

                return $adminSetting
                    ? $adminSettings[$adminSetting]
                    : $adminSettings;
            });
        } catch (\Exception $e) {
            // En caso de error, devolver valores predeterminados
            return $this->getDefaultAdminVars($adminSetting);
        }
    }

    private function getDefaultAdminVars($adminSetting = false): array
    {
        $defaultSettings = [
            'title'       => config('koneko.appTitle', 'Default Title'),
            'author'      => config('koneko.author', 'Default Author'),
            'description' => config('koneko.description', 'Default Description'),
            'favicon'     => $this->getFaviconPaths([]),
            'app_name'    => config('koneko.appName', 'Default App Name'),
            'image_logo'  => $this->getImageLogoPaths([]),
        ];

        return $adminSetting
            ? $defaultSettings[$adminSetting] ?? null
            : $defaultSettings;
    }

    private function buildAdminVarsArray(array $settings): array
    {
        return [
            'title'       => $settings['admin_title'] ?? config('koneko.appTitle'),
            'author'      => config('koneko.author'),
            'description' => config('koneko.description'),
            'favicon'     => $this->getFaviconPaths($settings),
            'app_name'    => $settings['admin_app_name'] ?? config('koneko.appName'),
            'image_logo'  => $this->getImageLogoPaths($settings),
        ];
    }

    public function getVuexyCustomizerVars()
    {
        // Obtener valores de la base de datos
        $settings = Setting::global()
            ->where('key', 'LIKE', 'vuexy_%')
            ->pluck('value', 'key')
            ->toArray();

        // Obtener configuraciones predeterminadas
        $defaultConfig = Config::get('vuexy.custom', []);

        // Mezclar las configuraciones predeterminadas con las de la base de datos
        return collect($defaultConfig)
            ->mapWithKeys(function ($defaultValue, $key) use ($settings) {
                $vuexyKey = 'vuexy_' . $key; // Convertir clave al formato de la base de datos

                // Obtener valor desde la base de datos o usar el predeterminado
                $value = $settings[$vuexyKey] ?? $defaultValue;

                // Forzar booleanos para claves específicas
                if (in_array($key, ['displayCustomizer', 'footerFixed', 'menuFixed', 'menuCollapsed', 'showDropdownOnHover'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                return [$key => $value];
            })
            ->toArray();
    }

    /**
     * Obtiene los paths de favicon en distintos tamaños.
     */
    private function getFaviconPaths(array $settings): array
    {
        $defaultFavicon = config('koneko.appFavicon');
        $namespace = $settings['admin_favicon_ns'] ?? null;

        return [
            'namespace' => $namespace,
            '16x16'     => $namespace ? "{$namespace}_16x16.png" : $defaultFavicon,
            '76x76'     => $namespace ? "{$namespace}_76x76.png" : $defaultFavicon,
            '120x120'   => $namespace ? "{$namespace}_120x120.png" : $defaultFavicon,
            '152x152'   => $namespace ? "{$namespace}_152x152.png" : $defaultFavicon,
            '180x180'   => $namespace ? "{$namespace}_180x180.png" : $defaultFavicon,
            '192x192'   => $namespace ? "{$namespace}_192x192.png" : $defaultFavicon,
        ];
    }

    /**
     * Obtiene los paths de los logos en distintos tamaños.
     */
    private function getImageLogoPaths(array $settings): array
    {
        $defaultLogo = config('koneko.appLogo');

        return [
            'small'       => $this->getImagePath($settings, 'admin_image_logo_small', $defaultLogo),
            'medium'      => $this->getImagePath($settings, 'admin_image_logo_medium', $defaultLogo),
            'large'       => $this->getImagePath($settings, 'admin_image_logo', $defaultLogo),
            'small_dark'  => $this->getImagePath($settings, 'admin_image_logo_small_dark', $defaultLogo),
            'medium_dark' => $this->getImagePath($settings, 'admin_image_logo_medium_dark', $defaultLogo),
            'large_dark'  => $this->getImagePath($settings, 'admin_image_logo_dark', $defaultLogo),
        ];
    }

    /**
     * Obtiene un path de imagen o retorna un valor predeterminado.
     */
    private function getImagePath(array $settings, string $key, string $default): string
    {
        return $settings[$key] ?? $default;
    }

    public static function clearAdminVarsCache()
    {
        Cache::forget("admin_settings");
    }
}
