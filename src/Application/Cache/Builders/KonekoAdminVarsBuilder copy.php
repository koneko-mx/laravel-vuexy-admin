<?php

namespace Koneko\VuexyAdmin\Application\Cache\Builders;

use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Cache\Services\KonekoVarsService;

/**
 * 🎛️ Builder de variables administrativas para el layout de administración.
 * - Fuente primaria: settings globales (namespace 'koneko.core.layout.admin')
 * - Permite override explícito por usuario autenticado.
 */
class ___KonekoAdminVarsBuilder
{
    public function __construct(
        protected KonekoVarsService $vars
    ) {
        $this->vars->context('layout', 'admin')->keyName('meta');
    }

    /**
     * Devuelve las variables visuales del layout administrativo.
     * Incluye título, autor, logos, favicon, etc.
     */
    public function get(): array
    {
        return $this->vars->remember('meta', fn () => $this->resolveAdminVars());
    }

    /**
     * Limpia el caché asociado al layout administrativo.
     */
    public function clear(): void
    {
        $this->vars->keyName('meta')->clear();
    }

    /**
     * Devuelve metainformación del contexto del builder (debug, auditoría).
     */
    public function info(): array
    {
        return [
            'context' => $this->vars->getContext(),
            'cache_key' => $this->vars->cacheKey('meta'),
            'source' => 'config + settings + optional user override',
        ];
    }

    /**
     * Construye el array de variables administrativas del layout.
     * Aplica override por usuario si está autenticado.
     */
    protected function resolveAdminVars(): array
    {
        $base = settings('core')->context('layout', 'admin')->asArray()->getSubGroup();

        return [
            'title'       => $base['title'] ?? config_m('core')->get('layout.admin.title', 'Koneko Admin'),
            'author'      => $base['author'] ?? config_m('core')->get('layout.admin.author', 'Default Author'),
            'description' => $base['description'] ?? config_m('core')->get('layout.admin.description', 'Default Description'),
            'favicon'     => $this->buildFaviconPaths($base),
            'app_name'    => $base['app_name'] ?? config_m('core')->get('app_name'),
            'image_logo'  => $this->buildImageLogoPaths($base),
        ];
    }

    /**
     * Construye el arreglo de rutas de favicon según configuración.
     */
    protected function buildFaviconPaths(array $settings): array
    {
        $ns = $settings['favicon_ns'] ?? null;
        $default = config_m('core')->get('favicon', 'favicon.ico');

        return [
            'namespace' => $ns,
            '16x16'     => $ns ? "{$ns}_16x16.png" : $default,
            '76x76'     => $ns ? "{$ns}_76x76.png" : $default,
            '120x120'   => $ns ? "{$ns}_120x120.png" : $default,
            '152x152'   => $ns ? "{$ns}_152x152.png" : $default,
            '180x180'   => $ns ? "{$ns}_180x180.png" : $default,
            '192x192'   => $ns ? "{$ns}_192x192.png" : $default,
        ];
    }

    /**
     * Construye el arreglo de rutas de logos según configuración.
     */
    protected function buildImageLogoPaths(array $settings): array
    {
        $default = config_m('core')->get('app_logo', 'logo-default.png');

        return [
            'small'       => $settings['image_logo_small'] ?? $default,
            'medium'      => $settings['image_logo_medium'] ?? $default,
            'large'       => $settings['image_logo'] ?? $default,
            'small_dark'  => $settings['image_logo_small_dark'] ?? $default,
            'medium_dark' => $settings['image_logo_medium_dark'] ?? $default,
            'large_dark'  => $settings['image_logo_dark'] ?? $default,
        ];
    }
}
