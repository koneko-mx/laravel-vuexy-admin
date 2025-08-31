<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Navbar;

use Illuminate\Contracts\Auth\Authenticatable;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;

class VuexyQuicklinksBuilder
{
    private const GROUP     = 'admin';
    private const SECTION   = 'layout';
    private const SUB_GROUP = 'navbar';
    private const KEY_NAME  = 'quicklinks';

    protected array $quicklinkRoutes = [];

    public function getUserQuicklinks(Authenticatable|int|null $user = null): array
    {
        $quickLinks = self::settings($user)->get(self::KEY_NAME, []);
        $quickLinks = $this->buildQuicklinks($quickLinks, $user);

        // dump($quickLinks);         die;
        return $quickLinks;
    }

    /*
    protected function getQuicklinks(Authenticatable|int|null $user = null): array
    {
        $menu  = self::getMenu($user);
        $extra = self::getExtraQuicklinks($user);

        $this->quicklinkRoutes = self::settings($user)
            ->asArray()
            ->getSubGroup() ?? [];

        $links = [];
        $this->collectFromMenu($menu, $links);

        foreach ($extra as $item) {
            if (in_array($item['route'], $this->quicklinkRoutes)) {
                $links[] = $item;
            }
        }
        dd($links);
        return [
            'totalLinks' => count($links),
            'rows'       => array_chunk($links, 2),
        ];
    }
    */

    protected function buildQuicklinks(array $routes, Authenticatable|int|null $user = null): array
    {
        $menu  = self::getMenu($user);
        $extra = self::getExtraQuicklinks($user);

        $this->quicklinkRoutes = $routes;

        $links = [];
        $this->collectFromMenu($menu, $links);

        foreach ($extra as $item) {
            if (in_array($item['route'], $this->quicklinkRoutes)) {
                $links[] = $item;
            }
        }

        return [
            'totalLinks' => count($links),
            'rows'       => array_chunk($links, 2),
        ];
    }

    public function addRoute(string $route, Authenticatable|int|null $user = null): void
    {
        $routes = self::settings($user)->get(self::KEY_NAME, []);

        if (count($routes) >= config_m()->get('layout.vuexy.maxQuickLinks', 12)) return;

        if (!in_array($route, $routes)) {
            $routes[] = $route;

            self::settings($user)->set(self::KEY_NAME, $routes);
        }
    }

    public function removeRoute(string $route, Authenticatable|int|null $user = null): void
    {
        $routes = self::settings($user)->get(self::KEY_NAME, []);

        // Filtra el arreglo para eliminar el valor igual al $route
        $routes = array_values(array_filter($routes, fn($r) => $r !== $route));

        self::settings($user)->set(self::KEY_NAME, $routes);
    }

    public function isRouteAllowed(string $routeId): bool
    {
        $menu  = self::getMenu();
        $extra = self::getExtraQuicklinks();

        $allAllowed = [];

        // Recolectar rutas estándar
        $this->collectAllRoutesFromMenu($menu, $allAllowed);

        foreach ($extra as $item) {
            if (isset($item['route'])) {
                $allAllowed[] = $item['route'];
            }
        }

        // Recolectar slugs de carpetas
        $slugs = [];
        $this->collectAllSlugsFromMenu($menu, $slugs);

        foreach ($slugs as $slug) {
            $allAllowed[] = "slug:$slug";
        }

        return in_array($routeId, $allAllowed, true);
    }

    public static function forgetCacheForUser(Authenticatable|int|null $user = null): void
    {
        self::settings($user)->forgetCache(self::KEY_NAME);
    }

    private function collectFromMenu(array $menu, array &$links, ?string $parent = null): void
    {
        foreach ($menu as $title => $item) {
            $route = $item['route'] ?? null;
            $slug  = $item['_slug'] ?? null;

            $routeId = $route ?? ($slug ? "slug:$slug" : null);

            if ($routeId && in_array($routeId, $this->quicklinkRoutes)) {
                $url = $route
                    ? route($route)
                    : ($slug ? route('admin.core.pages.folder.view', ['slug' => $slug]) : 'javascript:;');

                $item['icon'] = $slug ? 'ti ti-folders' : $item['icon'];

                $links[] = [
                    'title' => $title,
                    'subtitle' => $parent ?? config('app.name'),
                    'icon' => $item['icon'] ?? 'ti ti-point',
                    'url' => $url,
                    'route' => $routeId,
                ];
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->collectFromMenu($item['submenu'], $links, $title);
            }
        }
    }

    private function collectAllSlugsFromMenu(array $menu, array &$slugs): void
    {
        foreach ($menu as $item) {
            if (isset($item['_slug'])) {
                $slugs[] = $item['_slug'];
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->collectAllSlugsFromMenu($item['submenu'], $slugs);
            }
        }
    }

    private function collectAllRoutesFromMenu(array $menu, array &$routes): void
    {
        foreach ($menu as $item) {
            if (isset($item['route'])) {
                $routes[] = $item['route'];
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->collectAllRoutesFromMenu($item['submenu'], $routes);
            }
        }
    }

    private static function getMenu(Authenticatable|int|null|false $user = null): array
    {
        return app(VuexyMenuFormatter::class)->getMenu($user);
    }

    private static function getExtraQuicklinks(Authenticatable|int|null|false $user = null): array
    {
        return app(VuexyMenuFormatter::class)->getExtraQuicklinks($user);
    }

    private static function settings(Authenticatable|int|null|false $user = null): SettingsRepositoryInterface
    {
        return settings()
            ->context(self::GROUP, self::SECTION, self::SUB_GROUP)
            ->user($user);
    }
}
