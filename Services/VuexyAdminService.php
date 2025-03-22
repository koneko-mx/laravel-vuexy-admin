<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\{Auth,Cache,Route,Gate};
use Koneko\VuexyAdmin\Models\Setting;

class VuexyAdminService
{
    private $vuexySearch;
    private $quicklinksRouteNames = [];

    protected $cacheTTL = 60 * 24 * 30; // 30 días en minutos

    private $homeRoute = [
        'name' => 'Inicio',
        'route' => 'admin.core.home.index',
    ];

    private $user;

    public function __construct()
    {
        $this->user        = Auth::user();
        $this->vuexySearch = Auth::user() !== null;
    }

    public function getMenu()
    {
        // Obtener el menú desde la caché
        $menu = $this->user === null
            ? $this->getGuestMenu()
            : $this->getUserMenu();

        // Marcar la ruta actual como activa
        $currentRoute = Route::currentRouteName();

        return $this->markActive($menu, $currentRoute);
    }

    private function getGuestMenu()
    {
        return Cache::remember('vuexy_menu_guest', now()->addDays(7), function () {
            return $this->getMenuArray();
        });
    }

    private function getUserMenu()
    {
        //Cache::forget("vuexy_menu_user_{$this->user->id}"); // Borrar la caché anterior para actualizarla

        return Cache::remember("vuexy_menu_user_{$this->user->id}", now()->addHours(24), function () {
            return $this->getMenuArray();
        });
    }

    private function markActive($menu, $currentRoute)
    {
        foreach ($menu as &$item) {
            $item['active'] = false;

            // Check if the route matches
            if (isset($item['route']) && $item['route'] === $currentRoute)
                $item['active'] = true;

            // Process submenus recursively
            if (isset($item['submenu']) && !empty($item['submenu'])) {
                $item['submenu'] = $this->markActive($item['submenu'], $currentRoute);

                // If any submenu is active, mark the parent as active
                if (collect($item['submenu'])->contains('active', true))
                    $item['active'] = true;
            }
        }

        return $menu;
    }

    public static function clearUserMenuCache()
    {
        $user = Auth::user();

        if ($user !== null)
            Cache::forget("vuexy_menu_user_{$user->id}");
    }

    public static function clearGuestMenuCache()
    {
        Cache::forget('vuexy_menu_guest');
    }




    public function getSearch()
    {
        return $this->vuexySearch;
    }

    public function getVuexySearchData()
    {
        if ($this->user === null)
            return null;

        $pages = Cache::remember("vuexy_search_user_{$this->user->id}", now()->addDays(7), function () {
            return $this->cacheVuexySearchData();
        });

        // Formatear como JSON esperado
        return [
            'pages' => $pages,
        ];
    }

    private function cacheVuexySearchData()
    {
        $originalMenu = $this->getUserMenu();

        return  $this->getPagesSearchMenu($originalMenu);
    }

    private function getPagesSearchMenu(array $menu, string $parentPath = '')
    {
        $formattedMenu = [];

        foreach ($menu as $name => $item) {
            // Construir la ruta jerárquica (menu / submenu / submenu)
            $currentPath = $parentPath ? $parentPath . ' / ' . $name : $name;

            // Verificar si el elemento tiene una URL o una ruta
            $url = $item['url'] ?? (isset($item['route']) && route::has($item['route']) ? route($item['route']) : null);

            // Agregar el elemento al menú formateado
            if ($url) {
                $formattedMenu[] = [
                    'name' => $currentPath, // Usar la ruta completa
                    'icon' => $item['icon'] ?? 'ti ti-point',
                    'url' => $url,
                ];
            }

            // Si hay un submenú, procesarlo recursivamente
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $formattedMenu = array_merge(
                    $formattedMenu,
                    $this->getPagesSearchMenu($item['submenu'], $currentPath) // Pasar el path acumulado
                );
            }
        }

        return $formattedMenu;
    }

    public static function clearSearchMenuCache()
    {
        $user = Auth::user();

        if ($user !== null)
            Cache::forget("vuexy_search_user_{$user->id}");
    }




    public function getQuickLinks()
    {
        if ($this->user === null)
            return null;

        // Recuperar enlaces desde la caché
        $quickLinks = Cache::remember("vuexy_quick_links_user_{$this->user->id}", now()->addDays(7), function () {
            return $this->cacheQuickLinks();
        });

        // Verificar si la ruta actual está en la lista
        $currentRoute = Route::currentRouteName();
        $currentPageInList = $this->isCurrentPageInList($quickLinks, $currentRoute);

        // Agregar la verificación al resultado
        $quickLinks['current_page_in_list'] = $currentPageInList;

        return $quickLinks;
    }

    private function cacheQuickLinks()
    {
        $originalMenu = $this->getUserMenu();

        $quickLinks = [];

        $quicklinks = Setting::where('user_id', Auth::user()->id)
            ->where('key', 'quicklinks')
            ->first();

        $this->quicklinksRouteNames = $quicklinks ? json_decode($quicklinks->value, true) : [];

        // Ordenar y generar los quickLinks según el orden del menú
        $this->collectQuickLinksFromMenu($originalMenu, $quickLinks);

        $quickLinksData = [
            'totalLinks' => count($quickLinks),
            'rows' => array_chunk($quickLinks, 2), // Agrupar los atajos en filas de dos
        ];

        return $quickLinksData;
    }

    private function collectQuickLinksFromMenu(array $menu, array &$quickLinks, mixed $parentTitle = null)
    {
        foreach ($menu as $title => $item) {
            // Verificar si el elemento está en la lista de quicklinksRouteNames
            if (isset($item['route']) && in_array($item['route'], $this->quicklinksRouteNames)) {
                $quickLinks[] = [
                    'title' => $title,
                    'subtitle' => $parentTitle ?? env('APP_NAME'),
                    'icon' => $item['icon'] ?? 'ti ti-point',
                    'url' => isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#'),
                    'route' => $item['route'],
                ];
            }

            // Si tiene submenú, procesarlo recursivamente
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->collectQuickLinksFromMenu(
                    $item['submenu'],
                    $quickLinks,
                    $title // Pasar el título actual como subtítulo
                );
            }
        }
    }

    private function isCurrentPageInList(array $quickLinks, string $currentRoute): bool
    {
        foreach ($quickLinks['rows'] as $row) {
            foreach ($row as $link) {
                if (isset($link['route']) && $link['route'] === $currentRoute) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function clearQuickLinksCache()
    {
        $user = Auth::user();

        if ($user !== null)
            Cache::forget("vuexy_quick_links_user_{$user->id}");
    }




    public function getNotifications()
    {
        if ($this->user === null)
            return null;

        return Cache::remember("vuexy_notifications_user_{$this->user->id}", now()->addHours(4), function () {
            return $this->cacheNotifications();
        });
    }

    private function cacheNotifications()
    {
        return "<li class='nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2'>
                <a class='nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow' href='javascript:void(0);' data-bs-toggle='dropdown' data-bs-auto-close='outside' aria-expanded='false'>
                    <span class='position-relative'>
                        <i class='ti ti-bell ti-md'></i>
                    </span>
                </a>
            </li>";
    }

    public static function clearNotificationsCache()
    {
        $user = Auth::user();

        if ($user !== null)
            Cache::forget("vuexy_notifications_user_{$user->id}");
    }




    public function getBreadcrumbs()
    {
        $originalMenu = $this->user === null
            ? $this->getGuestMenu()
            : $this->getUserMenu();

        // Lógica para construir los breadcrumbs
        $breadcrumbs = $this->findBreadcrumbTrail($originalMenu);

        // Asegurar que el primer elemento siempre sea "Inicio"
        array_unshift($breadcrumbs, $this->homeRoute);

        return $breadcrumbs;
    }

    private function findBreadcrumbTrail(array $menu, array $breadcrumbs = []): array
    {
        foreach ($menu as $title => $item) {
            $skipBreadcrumb = isset($item['breadcrumbs']) && $item['breadcrumbs'] === false;

            $itemRoute = isset($item['route']) ? implode('.', array_slice(explode('.', $item['route']), 0, -1)): '';
            $currentRoute = implode('.', array_slice(explode('.', Route::currentRouteName()), 0, -1));

            if ($itemRoute === $currentRoute) {
                if (!$skipBreadcrumb) {
                    $breadcrumbs[] = [
                        'name' => $title,
                        'active' => true,
                    ];
                }

                return $breadcrumbs;
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $newBreadcrumbs = $breadcrumbs;

                if (!$skipBreadcrumb)
                    $newBreadcrumbs[] = [
                        'name' => $title,
                        'route' => $item['route'] ?? null,
                    ];

                $found = $this->findBreadcrumbTrail($item['submenu'], $newBreadcrumbs);

                if ($found)
                    return $found;
            }
        }

        return [];
    }




    private function getMenuArray()
    {
        $configMenu = config('vuexy_menu');

        return $this->filterMenu($configMenu);
    }

    private function filterMenu(array $menu)
    {
        $filteredMenu = [];

        foreach ($menu as $key => $item) {
            // Evaluar permisos con Spatie y eliminar elementos no autorizados
            if (isset($item['can']) && !$this->userCan($item['can'])) {
                continue;
            }

            if (isset($item['canNot']) && $this->userCannot($item['canNot'])) {
                continue;
            }

            // Si tiene un submenú, filtrarlo recursivamente
            if (isset($item['submenu'])) {
                $item['submenu'] = $this->filterMenu($item['submenu']);

                // Si el submenú queda vacío, eliminar el menú
                if (empty($item['submenu'])) {
                    continue;
                }
            }

            // Removemos los atributos 'can' y 'canNot' del resultado final
            unset($item['can'], $item['canNot']);

            if(isset($item['route']) && route::has($item['route'])){
                $item['url'] = route($item['route'])?? '';
            }

            // Agregar elemento filtrado al menú resultante
            $filteredMenu[$key] = $item;
        }

        return $filteredMenu;
    }

    private function userCan($permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (Gate::allows($permission)) {
                    return true; // Si tiene al menos un permiso, lo mostramos
                }
            }
            return true;
        }

        return Gate::allows($permissions);
    }

    private function userCannot($permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (Gate::denies($permission)) {
                    return true; // Si se le ha denegado al menos un permiso, lo ocultamos
                }
            }
            return false;
        }

        return Gate::denies($permissions);
    }
}
