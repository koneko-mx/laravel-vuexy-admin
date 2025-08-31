<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Navbar;

use Illuminate\Contracts\Auth\Authenticatable;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Models\User;

/**
 * 🔍 Generador del índice de búsqueda basado en el menú visible por el usuario.
 */
class VuexySearchBarBuilder
{
    private const GROUP     = 'website-admin';
    private const SECTION   = 'layout';
    private const SUB_GROUP = 'navbar';

    /** @var Model Scope */
    private const SCOPE = User::class;

    /** @var string Cache keyName */
    private const KEY_NAME = 'search-bar';

    /**
     * Obtiene el índice de búsqueda para el usuario autenticado.
     */
    public function getSearchData(Authenticatable|int|null $user): array
    {
        return [];

        return $this->rememberKeyCache(
            self::COMPONENT,
            self::GROUP,
            self::SUB_GROUP,
            self::CACHE_KEY,
            fn () => $this->buildIndex(),
            $user,
        );
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

    public static function forgetCacheForUser(Authenticatable|int|null $user): void
    {
        cache_m(self::COMPONENT, self::GROUP, self::SUB_GROUP)
            ->user($user)
            ->forget(self::CACHE_KEY);
    }

    public static function forgetVisitorCache(): void
    {
        cache_m(self::COMPONENT, self::GROUP, self::SUB_GROUP)
            ->user(false)
            ->forget(self::CACHE_KEY);
    }
}
