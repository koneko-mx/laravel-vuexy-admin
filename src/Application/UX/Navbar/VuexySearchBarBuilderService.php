<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Navbar;

use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

/**
 * 🔍 Generador del índice de búsqueda basado en el menú visible por el usuario.
 */
class VuexySearchBarBuilderService extends AbstractKeyValueCacheBuilder
{
    private const COMPONENT = 'core';
    private const GROUP     = 'layout';

    // Cache scope
    protected bool $isUserScoped = true;

    /** @var string Cache key */
    private const CACHE_KEY = 'navbar.search-bar';

    public function __construct()
    {
        parent::__construct(self::COMPONENT, self::GROUP, self::CACHE_KEY);
    }

    /**
     * Obtiene el índice de búsqueda para el usuario autenticado.
     */
    public function getSearchData(): array
    {
        return $this->rememberCache(self::CACHE_KEY, fn () => $this->buildIndex());
    }

    /**
     * Construye el índice de búsqueda a partir del menú procesado.
     */
    protected function buildIndex(): array
    {
        $menu = app(VuexyMenuFormatter::class)->getMenu();

        return [
            'pages'      => $this->buildPagesFromMenu($menu),
            'categories' => $this->buildCategoriesFromMenu($menu),
        ];
    }

    /**
     * Recorre el menú para construir el índice plano de rutas accesibles.
     *
     * @param array $menu Menú procesado
     * @param string $parent Prefijo del path (sin incluir el nombre actual)
     * @return array Indice plano de páginas [{ name, path, icon, url }]
     */
    protected function buildPagesFromMenu(array $menu, string $parent = ''): array
    {
        $entries = [];

        foreach ($menu as $title => $item) {
            // Ignorar claves que no representan ítems válidos
            if (str_starts_with($title, '_')) {
                continue;
            }

            $path = $parent;
            $fullPath = $parent ? "$parent / $title" : $title;

            if (isset($item['url'])) {
                $entries[] = [
                    'name' => $title,
                    'path' => $path, // ahora solo el camino de padres
                    'icon' => $item['icon'] ?? $item['_meta']['icon'] ?? 'ti ti-point',
                    'url'  => $item['url'],
                ];
            }

            // Recurse submenu
            if (!empty($item['submenu'])) {
                $entries = [
                    ...$entries,
                    ...$this->buildPagesFromMenu($item['submenu'], $fullPath)
                ];
            }
        }

        return $entries;
    }

    /**
     * Recorre el menú para construir un índice plano de categorías.
     *
     * @param array $menu Menú procesado
     * @param string $parent Prefijo del path
     * @return array Indice plano de categorías [{ name, path, slug, icon }]
     */
    protected function buildCategoriesFromMenu(array $menu, string $parent = ''): array
    {
        $categories = [];

        foreach ($menu as $title => $item) {
            // Ignorar claves que no representan ítems válidos
            if (str_starts_with($title, '_')) {
                continue;
            }

            $path = $parent;
            $fullPath = $parent ? "$parent / $title" : $title;

            // Solo procesar si tiene submenu (es categoría)
            if (!empty($item['submenu'])) {
                $slug = $item['_meta']['slug'] ?? $item['_slug'] ?? null;

                $categories[] = [
                    'name' => $title,
                    'path' => $path,
                    'slug' => $slug,
                    'icon' => $item['icon'] ?? $item['_meta']['icon'] ?? 'ti ti-folder',
                ];

                // Recurse submenu
                $categories = [
                    ...$categories,
                    ...$this->buildCategoriesFromMenu($item['submenu'], $fullPath)
                ];
            }
        }

        return $categories;
    }

    public static function forgetVisitorCache(): void
    {
        $instance = new static();

        $instance->user         = null;
        $instance->isUserScoped = true;

        $instance->forgetCache(self::CACHE_KEY);
    }

    public static function forgetCacheForUser(int $userId): void
    {
        $instance = new static();

        $instance->user         = app('auth')->getProvider()->retrieveById($userId);
        $instance->isUserScoped = true;

        $instance->forgetCache(self::CACHE_KEY);
    }
}
