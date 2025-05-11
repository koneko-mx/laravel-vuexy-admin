<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Navbar;

use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;

class VuexyQuicklinksBuilderService extends AbstractKeyValueCacheBuilder
{
    /** @var string Componente base */
    private const COMPONENT = 'core';
    private const GROUP     = 'layout';

    /** @var string Settings & Cache key */
    private const SETTINGS_KEY = 'navbar.quicklinks.user';

    /** @var bool Cache scope */
    protected bool $isUserScoped = true;

    // Quicklink routes
    private array $quicklinkRoutes = [];

    public function __construct()
    {
        parent::__construct(self::COMPONENT, self::GROUP);
    }

    public function getUserQuicklinks(): array
    {
        return $this->rememberCache(static::SETTINGS_KEY, fn () => $this->buildQuicklinks());
    }

    public function addRoute(string $route): void
    {
        $routes = settings()->setContext($this->component, $this->group)->get(static::SETTINGS_KEY, $this->user->id) ?? [];

        //if (count($routes) >= 20) return;

        if (!in_array($route, $routes)) {
            $routes[] = $route;

            settings()->setContext($this->component, $this->group)->set(static::SETTINGS_KEY, json_encode($routes), $this->user->id);
            $this->forgetCache(static::SETTINGS_KEY);
        }
    }

    public function removeRoute(string $route): void
    {
        $routes = settings()->setContext($this->component, $this->group)->get(static::SETTINGS_KEY, $this->user->id) ?? [];

        // Filtra el arreglo para eliminar el valor igual al $route
        $routes = array_values(array_filter($routes, fn($r) => $r !== $route));

        settings()->setContext($this->component, $this->group)->set(static::SETTINGS_KEY, json_encode($routes), $this->user->id);
        $this->forgetCache(static::SETTINGS_KEY);
    }

    private function buildQuicklinks(): array
    {
        $menu  = app(VuexyMenuFormatter::class)->getMenu();
        $extra = app(VuexyMenuFormatter::class)->getExtraQuicklinks();

        $this->quicklinkRoutes = settings()->setContext($this->component, $this->group)->get(static::SETTINGS_KEY, $this->user->id) ?? [];

        $links = [];
        $this->collectFromMenu($menu, $links);

        foreach ($extra as $item) {
            if (in_array($item['route'], $this->quicklinkRoutes)) {
                $links[] = $item;
            }
        }

        return [
            'totalLinks' => count($links),
            'rows' => array_chunk($links, 2),
        ];
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

    public function isRouteAllowed(string $routeId): bool
    {
        $menu  = app(VuexyMenuFormatter::class)->getMenu();
        $extra = app(VuexyMenuFormatter::class)->getExtraQuicklinks();

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

    public static function forgetCacheForUser(?int $userId = null): void
    {
        $instance = new static();

        $instance->user = $userId
            ? app('auth')->getProvider()->retrieveById($userId)
            : Auth::user();

        $instance->forgetCache(static::SETTINGS_KEY);
    }
}
