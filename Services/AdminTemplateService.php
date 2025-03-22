<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Models\Setting;

/**
 * Servicio para gestionar la configuración y personalización del template administrativo.
 *
 * Esta clase maneja las configuraciones del template VuexyAdmin, incluyendo variables
 * de personalización, logos, favicons y otras configuraciones de la interfaz.
 * Implementa un sistema de caché para optimizar el rendimiento.
 */
class AdminTemplateService
{
    /** @var int Tiempo de vida del caché en minutos (60 * 24 * 30 = 30 días) */
    protected $cacheTTL = 60 * 24 * 30;

    /**
     * Obtiene las variables de configuración del admin.
     *
     * @param string $setting Clave específica de configuración a obtener
     * @return array Configuraciones del admin o valor específico si se proporciona $setting
     */
    public function getAdminVars(string $setting = ''): array
    {
        try {
            // Verificar si el sistema está inicializado (la tabla `migrations` existe)
            if (!Schema::hasTable('migrations')) {
                return $this->getDefaultAdminVars($setting);
            }

            // Cargar desde el caché o la base de datos si está disponible
            $adminVars = Cache::remember('admin_settings', $this->cacheTTL, function () {
                $settings = Setting::withVirtualValue()
                    ->where('key', 'LIKE', 'admin.%')
                    ->pluck('value', 'key')
                    ->toArray();

                return $this->buildAdminVarsArray($settings);
            });

            return $setting ? ($adminVars[$setting] ?? []) : $adminVars;

        } catch (\Exception $e) {
            // En caso de error, devolver valores predeterminados
            return $this->getDefaultAdminVars($setting);
        }
    }

    /**
     * Obtiene las variables predeterminadas del admin.
     *
     * @param string $setting Clave específica de configuración a obtener
     * @return array Configuraciones predeterminadas o valor específico si se proporciona $setting
     */
    private function getDefaultAdminVars(string $setting = ''): array
    {
        $defaultSettings = [
            'title'       => config('koneko.appTitle', 'Default Title'),
            'author'      => config('koneko.author', 'Default Author'),
            'description' => config('koneko.description', 'Default Description'),
            'favicon'     => $this->getFaviconPaths([]),
            'app_name'    => config('koneko.appName', 'Default App Name'),
            'image_logo'  => $this->getImageLogoPaths([]),
        ];

        return $setting
            ? $defaultSettings[$setting] ?? null
            : $defaultSettings;
    }

    /**
     * Construye el array de variables del admin a partir de las configuraciones.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array estructurado con las variables del admin
     */
    private function buildAdminVarsArray(array $settings): array
    {
        return [
            'title'       => $settings['admin.title'] ?? config('koneko.appTitle'),
            'author'      => config('koneko.author'),
            'description' => $settings['admin.description'] ?? config('koneko.description'),
            'favicon'     => $this->getFaviconPaths($settings),
            'app_name'    => $settings['admin.app_name'] ?? config('koneko.appName'),
            'image_logo'  => $this->getImageLogoPaths($settings),
        ];
    }

    /**
     * Obtiene las variables de personalización de Vuexy.
     *
     * Combina las configuraciones predeterminadas con las almacenadas en la base de datos,
     * aplicando las transformaciones necesarias para tipos específicos como booleanos.
     *
     * @return array Array asociativo con las variables de personalización
     */
    public function getVuexyCustomizerVars()
    {
        // Obtener valores de la base de datos
        $settings = Setting::withVirtualValue()
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
                if (in_array($key, ['hasCustomizer', 'displayCustomizer', 'footerFixed', 'menuFixed', 'menuCollapsed', 'showDropdownOnHover'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                return [$key => $value];
            })
            ->toArray();
    }

    /**
     * Genera las rutas para los diferentes tamaños de favicon.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las rutas de los favicons en diferentes tamaños
     */
    private function getFaviconPaths(array $settings): array
    {
        $defaultFavicon = config('koneko.appFavicon');
        $namespace = $settings['admin.favicon_ns'] ?? null;

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
     * Genera las rutas para los diferentes tamaños y versiones del logo.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las rutas de los logos en diferentes tamaños y modos
     */
    private function getImageLogoPaths(array $settings): array
    {
        $defaultLogo = config('koneko.appLogo');

        return [
            'small'       => $this->getImagePath($settings, 'admin.image.logo_small', $defaultLogo),
            'medium'      => $this->getImagePath($settings, 'admin.image.logo_medium', $defaultLogo),
            'large'       => $this->getImagePath($settings, 'admin.image.logo', $defaultLogo),
            'small_dark'  => $this->getImagePath($settings, 'admin.image.logo_small_dark', $defaultLogo),
            'medium_dark' => $this->getImagePath($settings, 'admin.image.logo_medium_dark', $defaultLogo),
            'large_dark'  => $this->getImagePath($settings, 'admin.image.logo_dark', $defaultLogo),
        ];
    }

    /**
     * Obtiene la ruta de una imagen específica desde las configuraciones.
     *
     * @param array $settings Array asociativo de configuraciones
     * @param string $key Clave de la configuración
     * @param string $default Valor predeterminado si no se encuentra la configuración
     * @return string Ruta de la imagen
     */
    private function getImagePath(array $settings, string $key, string $default): string
    {
        return $settings[$key] ?? $default;
    }

    /**
     * Limpia el caché de las variables del admin.
     *
     * @return void
     */
    public static function clearAdminVarsCache()
    {
        Cache::forget("admin_settings");
    }
}
