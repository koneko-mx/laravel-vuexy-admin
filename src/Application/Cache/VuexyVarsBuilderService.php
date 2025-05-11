<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Cache;

use Illuminate\Support\Facades\{Cache,Config,Schema};
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

/**
 * 🎛️ Servicio de gestión de variables Vuexy Admin y Customizer.
 */
class VuexyVarsBuilderService extends AbstractKeyValueCacheBuilder
{
    // Namespace base
    private const COMPONENT = 'core';
    private const GROUP     = 'layout';

    //protected string $group     = 'layout-builder';

    // Cache scope
    protected bool $isUserScoped = true;

    /** @var string Settings & Cache key */
    private const SETTINGS_ADMIN_VARS_KEY     = 'admin-vars';
    public const SETTINGS_CUSTOMIZER_VARS_KEY = 'customizer-vars';

    public function __construct()
    {
        parent::__construct(self::COMPONENT, self::GROUP);
    }

    /**
     * Obtiene las variables administrativas principales.
     */
    public function getAdminVars(?string $key = null): mixed
    {
        /*
        if (!Schema::hasTable('settings')) {
            return $this->getDefaultAdminVars($key);
        }
        */

        $vars = $this->rememberCache(self::SETTINGS_ADMIN_VARS_KEY, function () {
            $settings = settings()->setContext($this->component, $this->group)->getGroup(self::SETTINGS_ADMIN_VARS_KEY);

            return $this->buildAdminVarsArray($settings);
        });

        return $key ? ($vars[$key] ?? null) : $vars;
    }

    /**
     * Obtiene las configuraciones del customizador Vuexy.
     */
    public function getVuexyCustomizerVars(): array
    {
        /*
        if (!Schema::hasTable('settings')) {
            return $this->getDefaultVuexyVars();
        }
        */

        return $this->rememberCache(self::SETTINGS_CUSTOMIZER_VARS_KEY, function () {
            $settings = settings()->setContext($this->component, $this->group)->getGroup(self::SETTINGS_CUSTOMIZER_VARS_KEY);

            return $this->buildVuexyCustomizerVars($settings);
        });
    }


    /**
     * Elimina las configuraciones del customizador Vuexy.
     */
    public static function deleteVuexyCustomizerVars(): void
    {
        $instance = new static();

        $instance->setContext($instance->component, $instance->group);

        settings()->setContext($instance->component, $instance->group);
        settings()->deleteGroup(self::SETTINGS_CUSTOMIZER_VARS_KEY);

        Cache::forget($instance->generateCacheKey(self::SETTINGS_CUSTOMIZER_VARS_KEY));
        Cache::forget(cache_manager($instance->component, $instance->group)->key(self::SETTINGS_CUSTOMIZER_VARS_KEY));
    }

    /**
     * Limpia las caches del admin y del customizador Vuexy.
     */
    public static function clearCache(): void
    {
        //Cache::forget(self::SETTINGS_ADMIN_VARS_KEY);
        //Cache::forget(self::SETTINGS_CUSTOMIZER_VARS_KEY);
    }

    /**
     * Construye las variables del admin.
     */
    private function buildAdminVarsArray(array $settings): array
    {
        return [
            'title'       => $settings['title'] ?? config("{$this->namespace}.title", 'Default Title'),
            'author'      => $settings['author'] ?? config("{$this->namespace}.author", 'Default Author'),
            'description' => $settings['description'] ?? config("{$this->namespace}.description", 'Default Description'),
            'favicon'     => $this->buildFaviconPaths($settings),
            'app_name'    => $settings['app_name'] ?? config("{$this->namespace}.app_name", 'Default App Name'),
            'image_logo'  => $this->buildImageLogoPaths($settings),
        ];
    }

    /**
     * Construye las variables de Vuexy customizer.
     */
    private function buildVuexyCustomizerVars(array $settings): array
    {
        $defaults = config("{$this->namespace}.admin.vuexy");

        return collect($defaults)
            ->mapWithKeys(function ($defaultValue, $key) use ($settings) {
                $vuexyKey = $key;
                $value    = $settings[$vuexyKey] ?? $defaultValue;

                if (in_array($key, [
                    'hasCustomizer', 'displayCustomizer', 'footerFixed',
                    'menuFixed', 'menuCollapsed', 'showDropdownOnHover'
                ], true)) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                return [$key => $value];
            })
            ->toArray();
    }

    /**
     * Construye las rutas de favicon.
     */
    private function buildFaviconPaths(array $settings): array
    {
        $namespace      = $settings['favicon_ns'] ?? null;
        $defaultFavicon = config("{$this->namespace}.favicon", 'favicon.ico');

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
     * Construye las rutas de logos.
     */
    private function buildImageLogoPaths(array $settings): array
    {
        $defaultLogo = config("{$this->namespace}.app_logo", 'logo-default.png');

        return [
            'small'       => $settings['image_logo_small'] ?? $defaultLogo,
            'medium'      => $settings['image_logo_medium'] ?? $defaultLogo,
            'large'       => $settings['image_logo'] ?? $defaultLogo,
            'small_dark'  => $settings['image_logo_small_dark'] ?? $defaultLogo,
            'medium_dark' => $settings['image_logo_medium_dark'] ?? $defaultLogo,
            'large_dark'  => $settings['image_logo_dark'] ?? $defaultLogo,
        ];
    }

    /**
     * Valores de fallback si no hay base de datos.
     */
    private function getDefaultAdminVars(?string $key = null): array
    {
        return $key
            ? ($this->buildAdminVarsArray([])[$key] ?? null)
            : $this->buildAdminVarsArray([]);
    }

    /**
     * Valores de fallback para customizer Vuexy.
     */
    private function getDefaultVuexyVars(): array
    {
        return Config::get("{$this->namespace}.admin.vuexy", []);
    }


}