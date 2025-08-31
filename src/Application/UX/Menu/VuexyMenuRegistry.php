<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

use Illuminate\Support\Facades\File;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;

class VuexyMenuRegistry
{
    /**
     * Menú combinado crudo desde todos los orígenes (core, componentes, proyecto)
     */
    protected array $mergedMenu = [];

    public function __construct()
    {
        $this->mergedMenu = $this->buildMergedMenu();
    }

    /**
     * Devuelve el menú combinado crudo, sin procesar
     */
    public function getMerged(): array
    {
        return $this->mergedMenu;
    }

    /**
     * Realiza el merge recursivo y validación (si está habilitada)
     */
    protected function buildMergedMenu(): array
    {
        $sources = $this->loadAllMenus();
        $merged  = [];

        foreach ($sources as $source) {
            $merged = $this->mergeMenus($merged, $source);
        }

        return $merged;
    }

    /**
     * Carga todos los orígenes de menú válidos
     */
    protected function loadAllMenus(): array
    {
        return array_filter([
            $this->loadCoreMenu(),
            ...$this->loadComponentMenus(),
            $this->loadProjectMenu(),
        ]);
    }

    /**
     * Menú del core, si existe
     */
    protected function loadCoreMenu(): array
    {
        $fullPath = base_path('vendor/koneko/laravel-vuexy-admin/config/vuexy_admin_menu.php');

        if (File::exists($fullPath)) {
            $menu = require $fullPath;

            // verifica si la clave Inicio aparece aquí
            return $this->injectComponentMeta($menu, 'core');
        }

        return [];
    }

    /**
     * Menús de los componentes instalados (por convención)
     */
    protected function loadComponentMenus(): array
    {
        $paths = [];

        foreach (KonekoModuleRegistry::enabled() as $module) {
            $menuPath = $module->extensions['menu']['path'] ?? null;

            if ($menuPath) {
                $fullPath = $module->basePath . DIRECTORY_SEPARATOR . $menuPath;

                if (File::exists($fullPath)) {
                    $paths[] = [
                        'path'      => $fullPath,
                        'component' => $module->componentNamespace,
                    ];
                }
            }
        }

        return collect($paths)
            ->map(fn($data) => $this->injectComponentMeta(require $data['path'], $data['component']))
            ->filter(fn($v) => is_array($v))
            ->values()->all();
    }

    /**
     * Menú del proyecto principal (custom)
     */
    protected function loadProjectMenu(): array
    {
        $menuConfig = config('vuexy-admin.extensions.menu');

        if (!$menuConfig || !isset($menuConfig['path'])) {
            return [];
        }

        $path = $menuConfig['path'];

        $fullPath = str_starts_with($path, 'storage/')
            ? storage_path(substr($path, strlen('storage/')))
            : base_path($path);

        return File::exists($fullPath)
            ? $this->injectComponentMeta(require $fullPath, 'project')
            : [];
    }

    protected function injectComponentMeta(array $menu, string $component): array
    {
        foreach ($menu as &$item) {
            if (!is_array($item)) continue;

            // Verificaos Extra Quicklinks
            if (isset($item['_quicklinks'])) {
                $item['_quicklinks'] = $this->injectComponentMeta($item['_quicklinks'], $component);
                continue;
            }

            // Inyectar en _meta solo si 'component' no está definido
            $item['_meta'] ??= [];

            if (!isset($item['_meta']['component'])) {
                $item['_meta']['component'] = $component;
            }

            // Aplicar recursivamente si tiene submenu
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $item['submenu'] = $this->injectComponentMeta($item['submenu'], $component);
            }
        }

        return $menu;
    }

    /**
     * Realiza un merge recursivo entre dos menús jerárquicos,
     * respetando subniveles y sobrescribiendo claves compatibles.
     */
    protected function mergeMenus(array $base, array $overrides): array
    {
        foreach ($overrides as $key => $overrideItem) {
            // Si la clave ya existe en el menú base
            if (isset($base[$key])) {
                // Si ambos son arrays con submenu, hacer merge profundo
                if (is_array($overrideItem) && is_array($base[$key])) {
                    $merged = $base[$key];

                    // Merge especial de _meta si ambos lo tienen
                    if (isset($merged['_meta']) && isset($overrideItem['_meta'])) {
                        $overrideMeta = $overrideItem['_meta'];
                        $baseMeta     = $merged['_meta'];

                        // Preservar el component original del base
                        if (isset($baseMeta['component'])) {
                            $overrideMeta['component'] = $baseMeta['component'];
                        }

                        $merged['_meta'] = array_merge($baseMeta, $overrideMeta);
                        unset($overrideItem['_meta']);
                    }

                    // Si hay submenu en ambos, hacer merge recursivo
                    if (isset($merged['submenu']) && isset($overrideItem['submenu'])) {
                        $merged['submenu'] = static::mergeMenus($merged['submenu'], $overrideItem['submenu']);
                        unset($overrideItem['submenu']);
                    }

                    // Resto de propiedades se sobrescriben
                    $base[$key] = array_merge($merged, $overrideItem);

                } else {
                    // Si no son arrays compatibles, sobrescribe completo
                    $base[$key] = $overrideItem;
                }

            } else {
                // Clave nueva, simplemente agregar
                $base[$key] = $overrideItem;
            }
        }

        return $base;
    }
}
