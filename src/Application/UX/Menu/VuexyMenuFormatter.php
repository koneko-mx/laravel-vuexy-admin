<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Config\Contracts\ConfigRepositoryInterface;
use Koneko\VuexyAdmin\Application\Settings\Contracts\SettingsRepositoryInterface;
use Koneko\VuexyAdmin\Support\Traits\Auth\HasResolvableUser;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class VuexyMenuFormatter
{
    use HasResolvableUser;

    private const GROUP   = 'layout';
    private const SECTION = 'menu';

    private const MENU_KEY_NAME             = 'menu';
    private const EXTRA_QUICKLINKS_KEY_NAME = 'extra-quicklinks';

    private static function settings(Authenticatable|int|null|false $user = null): SettingsRepositoryInterface
    {
        return settings()
            ->context(self::GROUP, self::SECTION)
            ->user($user);
    }

    private static function config(): ConfigRepositoryInterface
    {
        return config_m()->context(self::GROUP, self::SECTION, 'debug');
    }

    public function getMenu(Authenticatable|int|null|false $user = null): array
    {
        $menu = self::settings($user)
            ->keyName(self::MENU_KEY_NAME)
            ->remember(fn () => $this->format($user));

        $this->markActiveTrail($menu);

        return $menu;
    }

    public function getMenuBySlug(string $slug, Authenticatable|int|null|false $user = null): ?array
    {
        $menu = $this->getMenu($user);

        return $this->findInMenuWithKey($menu, fn($item) => ($item['_slug'] ?? null) === $slug);
    }

    public function getMenuByAutoId(int $autoId, Authenticatable|int|null|false $user = null): ?array
    {
        $menu = $this->getMenu($user);

        return $this->findInMenuWithKey($menu, fn($item) => ($item['_meta']['auto_id'] ?? null) === $autoId);
    }

    public function getExtraQuicklinks(Authenticatable|int|null|false $user = null): array
    {
        return self::settings($user)
            ->keyName(self::EXTRA_QUICKLINKS_KEY_NAME)
            ->remember(fn () => $this->buildExtraQuicklinks($user));
    }

    protected function format(Authenticatable|int|null|false $user = null): array
    {
        $rawMenu = $this->getRawMenu();
        $menu    = $this->processRecursive($rawMenu);

        $this->assignAutoIds($menu);
        $this->assignSlugs($menu);
        $this->assignCounts($menu);

        if (self::config()->get('show_disallowed_links', false)
            || self::config()->get('show_hidden_items', false)
            || self::config()->get('show_broken_routes', false)
        ) {
            $this->markDebugFlags($menu, $user);
        }

        unset($menu['_extra']);

        return $menu;
    }

    protected function buildExtraQuicklinks(Authenticatable|int|null|false $user = null): array
    {
        $extra = $this->getRawMenu()['_extra']['_quicklinks'] ?? [];
        $links = [];

        foreach ($extra as $key => $item) {
            if (!$this->isVisible($item, $user)) continue;

            $this->convertRouteToUrl($item);

            $links[] = [
                'title'    => $key,
                'subtitle' => config('app.name'),
                'icon'     => $item['icon'] ?? 'ti ti-point',
                'url'      => $item['url'] ?? 'javascript:;',
                'route'    => $item['route'] ?? null,
            ];
        }

        return $links;
    }

    protected function getRawMenu(): array
    {
        return app(VuexyMenuRegistry::class)->getMerged();
    }

    /**
     * Recorre recursivamente el menú para encontrar el ítem que cumpla con la condición.
     */
    protected function findInMenuWithKey(array $menu, callable $condition, array $trail = []): ?array
    {
        foreach ($menu as $key => $item) {
            $currentTrail = [...$trail, ['key' => $key, 'label' => $item['_meta']['widget_label'] ?? $item['_meta']['original_key'] ?? $key]];

            if ($condition($item)) {
                $item['_trail'] = $currentTrail;
                return [$key => $item];
            }

            if (!empty($item['submenu'])) {
                $found = $this->findInMenuWithKey($item['submenu'], $condition, $currentTrail);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    protected function processRecursive(array $menu): array
    {
        $result = [];

        foreach ($menu as $key => $item) {
            // Si el ítem no es visible, se descarta (excepto debug)
            if (!$this->isVisible($item)) {
                continue;
            }

            // Procesar ruta
            if (!isset($item['url']) && isset($item['route'])) {
                $this->convertRouteToUrl($item);
            }

            // Procesar submenu
            if (!empty($item['submenu'])) {
                $item['submenu'] = $this->processRecursive($item['submenu']);

                if (empty($item['submenu']) && !isset($item['route']) && !isset($item['url'])) {
                    continue;
                }
            }

            $result[$key] = $item;
        }

        return $this->applySorting($result);
    }

    protected function applySorting(array $items): array
    {
        $sorted = $this->sortLevel($items);

        foreach ($sorted as &$item) {
            if (!empty($item['submenu']) && is_array($item['submenu'])) {
                $item['submenu'] = $this->applySorting($item['submenu']); // post-sort, no pre
            }
        }

        return $sorted;
    }

    protected function sortLevel(array $items): array
    {
        $nodes = [];

        foreach ($items as $key => $item) {
            $meta     = $item['_meta'] ?? [];
            $priority = $meta['priority'] ?? $item['priority'] ?? 500;
            $before   = $meta['before_to'] ?? $item['before_to'] ?? null;
            $after    = $meta['after_to']  ?? $item['after_to']  ?? null;

            $priority = match (true) {
                $priority === 'first' => -999999,
                $priority === 'last'  => 999999,
                is_numeric($priority) => (int) $priority,
                default               => 500,
            };

            $nodes[$key] = compact('key', 'item', 'before', 'after', 'priority');
        }

        uasort($nodes, fn($a, $b) => $a['priority'] <=> $b['priority']);

        $sorted = [];

        while (!empty($nodes)) {
            $progress = false;

            foreach ($nodes as $key => $node) {
                $canInsert = true;

                if ($node['before'] && !isset($sorted[$node['before']])) {
                    $canInsert = false;
                }

                if ($node['after'] && !isset($sorted[$node['after']])) {
                    $canInsert = false;
                }

                if (!$canInsert) {
                    continue;
                }

                if ($node['before'] && isset($sorted[$node['before']])) {
                    $position = array_search($node['before'], array_keys($sorted), true);
                    $sorted = array_slice($sorted, 0, $position, true)
                            + [$key => $node['item']]
                            + array_slice($sorted, $position, null, true);

                } elseif ($node['after'] && isset($sorted[$node['after']])) {
                    $position = array_search($node['after'], array_keys($sorted), true) + 1;
                    $sorted = array_slice($sorted, 0, $position, true)
                            + [$key => $node['item']]
                            + array_slice($sorted, $position, null, true);

                } else {
                    $sorted[$key] = $node['item'];
                }

                unset($nodes[$key]);
                $progress = true;
            }

            if (!$progress) {
                foreach ($nodes as $key => $node) {
                    $sorted[$key] = $node['item'];
                }
                break;
            }
        }

        return $sorted;
    }

    protected function isVisible(array $item, Authenticatable|int|null|false $user = null): bool
    {
        if (isset($item['_meta']['visible']) && $item['_meta']['visible'] === false)
            return false;

        if (isset($item['visible']) && $item['visible'] === false)
            return false;

        if (isset($item['can']) && !$this->userCan($user, $item['can']))
            return self::config()->get('show_disallowed_links', false);

        if (isset($item['route']) && !Route::has($item['route']))
            return self::config()->get('show_broken_routes', false);

        return true;
    }

    protected function userCan(Authenticatable|int|null|false $user, string|array $permissions): bool
    {
        $user = $this->resolveUser($user);

        if (!$user || !method_exists($user, 'hasPermissionTo')) { return false; }

        try {
            if (is_array($permissions)) {
                foreach ($permissions as $perm) {
                    if ($user->hasPermissionTo($perm)) return true;
                }

                return false;
            }

            return $user->hasPermissionTo($permissions);

        } catch (PermissionDoesNotExist) {
            return false;
        }
    }

    protected function convertRouteToUrl(array &$item): void
    {
        if (isset($item['route'])) {
            $item['url'] = Route::has($item['route'])
                ? route($item['route'])
                : 'javascript:;';
        }
    }

    protected function assignAutoIds(array &$menu, int &$index = 0, string $prefix = ''): void
    {
        foreach ($menu as $key => &$item) {
            $item['_meta']['auto_id'] = $index++;

            if (!empty($item['submenu'])) {
                $this->assignAutoIds($item['submenu'], $index, $prefix . $key . '/');
            }
        }
    }

    protected function assignSlugs(array &$menu, string $trail = ''): void
    {
        foreach ($menu as $key => &$item) {
            $currentTrail = trim($trail . ' ' . $key);
            $slug = Str::slug($currentTrail);

            if (!empty($item['submenu']) && empty($item['route'])) {
                $item['_slug'] = $slug;
                $item['_path'] = $currentTrail;
            }

            if (!empty($item['submenu'])) {
                $this->assignSlugs($item['submenu'], $currentTrail . ' /');
            }
        }
    }

    protected function assignCounts(array &$menu): void
    {
        foreach ($menu as &$item) {
            if (!empty($item['submenu'])) {
                $this->assignCounts($item['submenu']);

                // Calcula el total recursivamente
                $item['_meta']['count'] = count($item['submenu']);

            } else {
                $item['_meta']['count'] = 0;
            }
        }
    }

    protected function markActiveTrail(array &$menu): void
    {
        $currentRoute = Route::currentRouteName();
        $currentUrl   = request()->url();

        $markTrail = function (&$items, $trail = []) use (&$markTrail, $currentRoute, $currentUrl): bool {
            foreach ($items as &$item) {
                $match = false;

                if (isset($item['route']) && $item['route'] === $currentRoute) {
                    $match = true;

                } elseif (isset($item['url']) && Str::is($item['url'], $currentUrl)) {
                    $match = true;
                }

                if (!empty($item['submenu'])) {
                    $match = $markTrail($item['submenu'], [...$trail, &$item]) || $match;
                }

                if ($match) {
                    $item['_is_active_item'] = true;

                    foreach ($trail as &$parent) {
                        $parent['_is_active_parent'] = true;
                    }

                    return true;
                }
            }

            return false;
        };

        $markTrail($menu);
    }

    protected function markDebugFlags(array &$menu, Authenticatable|int|null|false $user): void
    {
        foreach ($menu as &$item) {
            // Flag: el usuario no tiene permiso
            if (self::config()->get('show_disallowed_links', false)) {
                $item['_meta']['disallowed_link'] = isset($item['can']) && !$this->userCan($user, $item['can']);
            }

            // Flag: está marcado como oculto
            if (self::config()->get('show_hidden_items', false)) {
                $item['_meta']['hidden_item'] = isset($item['_meta']['visible']) && $item['_meta']['visible'] === false;
            }

            // Flag: la ruta no existe
            if (self::config()->get('show_broken_routes', false)) {
                $item['_meta']['broken_route'] = isset($item['route']) && !Route::has($item['route']);
            }

            if (!empty($item['submenu'])) {
                $this->markDebugFlags($item['submenu'], $user);
            }
        }
    }

    public static function forgetCacheForUser(Authenticatable|int|null|false $user = null): void
    {
        self::settings($user)
            ->keyName(self::MENU_KEY_NAME)
            ->forgetCache();

        self::settings($user)
            ->keyName(self::EXTRA_QUICKLINKS_KEY_NAME)
            ->forgetCache();
    }
}
