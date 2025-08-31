<?php

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

use Koneko\VuexyAdmin\Application\UX\ImageHandler\WebAdminImageHandler;

/**
 * 🎛️ Builder de variables administrativas para el layout de administración.
 * - Permite override explícito por usuario autenticado.
 */
class KonekoAdminVarsBuilder
{
    private $group   = 'layout';
    private $section = 'admin';
    private $keyName = 'meta';

    /**
     * Devuelve las variables visuales del layout administrativo.
     * Incluye título, autor, logos, favicon, etc.
     */
    public function get(): array
    {
        return settings()
            ->context($this->group, $this->section)
            ->keyName($this->keyName)
            ->remember(fn () => $this->resolveAdminVars());
    }

    /**
     * Limpia el caché asociado al layout administrativo.
     */
    public function clear(): void
    {
        settings()
            ->context($this->group, $this->section)
            ->forgetCache($this->keyName);
    }

    /**
     * Construye el array de variables administrativas del layout.
     * Aplica override por usuario si está autenticado.
     */
    protected function resolveAdminVars(): array
    {
        $base = settings()
            ->context($this->group, $this->section, null)
            ->asArray()
            ->all();

        return [
            'title'       => $base['title'] ?? config('koneko.title', 'Koneko Admin'),
            'author'      => $base['author'] ?? config('koneko.author', 'Default Author'),
            'description' => $base['description'] ?? config('koneko.description', 'Default Description'),
            'favicon'     => $this->buildFaviconPaths($base),
            'app_name'    => $base['app_name'] ?? config('koneko.app_name', 'Koneko Admin'),
            'image_logo'  => $this->buildImageLogoPaths($base),
        ];
    }

    /**
     * Construye el arreglo de rutas de favicon según configuración.
     */
    protected function buildFaviconPaths(array $settings): array
    {
        $ns = isset($settings['favicon_ns']) && $settings['favicon_ns']
            ? WebAdminImageHandler::FAVICON_BASE_PATH . $settings['favicon_ns']
            : '';

        $favicon_default = '../vendor/vuexy-admin/img/logo/koneko-04.png';

        return [
            'namespace' => $ns,
            '16x16'     => $ns ? "{$ns}_16x16.png" : $favicon_default,
            '76x76'     => $ns ? "{$ns}_76x76.png" : $favicon_default,
            '120x120'   => $ns ? "{$ns}_120x120.png" : $favicon_default,
            '152x152'   => $ns ? "{$ns}_152x152.png" : $favicon_default,
            '180x180'   => $ns ? "{$ns}_180x180.png" : $favicon_default,
            '192x192'   => $ns ? "{$ns}_192x192.png" : $favicon_default,
        ];
    }

    /**
     * Construye el arreglo de rutas de logos según configuración.
     */
    protected function buildImageLogoPaths(array $settings): array
    {
        $logo_default = '../vendor/vuexy-admin/img/logo/koneko-04.png';
        $path = WebAdminImageHandler::LOGO_BASE_PATH;

        return [
            'small'       => isset($settings['image_logo_small'])          ? $path . $settings['image_logo_small']          : $logo_default,
            'medium'      => isset($settings['image_logo_medium'])         ? $path . $settings['image_logo_medium']         : $logo_default,
            'large'       => isset($settings['image_logo'])                ? $path . $settings['image_logo']                : $logo_default,
            'small_dark'  => isset($settings['image_logo_small_dark'])     ? $path . $settings['image_logo_small_dark']     : $logo_default,
            'medium_dark' => isset($settings['image_logo_medium_dark'])    ? $path . $settings['image_logo_medium_dark']    : $logo_default,
            'large_dark'  => isset($settings['image_logo_dark'])           ? $path . $settings['image_logo_dark']           : $logo_default,
        ];
    }
}
