<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Menu;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth , Route};
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Support\Cache\AbstractKeyValueCacheBuilder;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class VuexyMenuFormatter extends AbstractKeyValueCacheBuilder
{
    /** @var string Componente base */
    private const COMPONENT = 'core';
    private const GROUP     = 'layout';

    /** @var string Cache key */
    private const CACHE_KEY = 'menu';

    /** @var bool Cache scope */
    protected bool $isUserScoped = true;

    /** @var array Opciones de formato */
    private array $options = [];

    public function __construct()
    {
        parent::__construct(self::COMPONENT, self::GROUP);
    }

    public function getMenu(false|null|Authenticatable $user = null, array $options = []): array
    {
        $this->user = $user === false ? null : ($user ?? Auth::user());

        $this->options = $options;
        $this->isUserScoped = $user !== false;

        $menu = $this->rememberCache(self::CACHE_KEY, fn () => $this->format());

        $this->markActiveTrail($menu);

        return $menu;
    }

    public function getMenuBySlug(string $slug, false|null|Authenticatable $user = null, array $options = []): ?array
    {
        $menu = $this->getMenu($user, $options);

        return $this->findInMenuWithKey($menu, fn($item) => ($item['_slug'] ?? null) === $slug);
    }

    public function getMenuByAutoId(int $autoId, false|null|Authenticatable $user = null, array $options = []): ?array
    {
        $menu = $this->getMenu($user, $options);

        return $this->findInMenuWithKey($menu, fn($item) => ($item['_meta']['auto_id'] ?? null) === $autoId);
    }

    /**
     * Retorna los ítems definidos en _extra_quicklinks del menú, procesados como atajos rápidos.
     */
    public function getExtraQuicklinks(false|null|Authenticatable $user = null): array
    {
        $this->user         = $user === false ? null : ($user ?? Auth::user());
        $this->isUserScoped = !is_null($this->user);

        $rawMenu = app(VuexyMenuRegistry::class)->getMerged();

        $extra = $rawMenu['_extra_quicklinks'] ?? [];

        $links = [];

        foreach ($extra as $key => $item) {
            if (!$this->isVisible($item)) continue;

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

    protected function format(): array
    {
        $rawMenu = app(VuexyMenuRegistry::class)->getMerged();

        $menu = $this->processRecursive($rawMenu);

        $this->assignAutoIds($menu);
        $this->assignSlugs($menu);
        $this->assignCounts($menu);

        if (
            config('koneko.admin.menu.debug.show_disallowed_links', false)
            || config('koneko.admin.menu.debug.show_hidden_items', false)
            || config('koneko.admin.menu.debug.show_broken_routers', false)
        ) {
            $this->markDebugFlags($menu);
        }

        unset($menu['_extra_quicklinks']);

        return $menu;
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

    protected function isVisible(array $item): bool
    {
        if (isset($item['_meta']['visible']) && $item['_meta']['visible'] === false)
            return false;

        if (isset($item['visible']) && $item['visible'] === false)
            return false;

        if (isset($item['can']) && !$this->userCan($item['can']))
            return config('koneko.admin.menu.debug.show_disallowed_links', false);

        if (isset($item['route']) && !Route::has($item['route']))
            return config('koneko.admin.menu.debug.show_broken_routers', false);

        return true;
    }

    protected function userCan(string|array $permissions): bool
    {
        if (!$this->user || !method_exists($this->user, 'hasPermissionTo')) {
            return false;
        }

        try {
            if (is_array($permissions)) {
                foreach ($permissions as $perm) {
                    if ($this->user->hasPermissionTo($perm)) return true;
                }

                return false;
            }

            return $this->user->hasPermissionTo($permissions);

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
        $currentUrl = request()->url();

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

    protected function markDebugFlags(array &$menu): void
    {
        foreach ($menu as &$item) {
            // Flag: el usuario no tiene permiso
            if (config('koneko.admin.menu.debug.show_disallowed_links', false)) {
                $item['_meta']['disallowed_link'] = isset($item['can']) && !$this->userCan($item['can']);
            }

            // Flag: está marcado como oculto
            if (config('koneko.admin.menu.debug.show_hidden_items', false)) {
                $item['_meta']['hidden_item'] = isset($item['_meta']['visible']) && $item['_meta']['visible'] === false;
            }

            // Flag: la ruta no existe
            if (config('koneko.admin.menu.debug.show_broken_routers', false)) {
                $item['_meta']['broken_route'] = isset($item['route']) && !Route::has($item['route']);
            }

            if (!empty($item['submenu'])) {
                $this->markDebugFlags($item['submenu']);
            }
        }
    }

    public static function forgetCacheForUser(?int $userId = null): void
    {
        $instance = new static();

        $instance->user         = $userId
            ? app('auth')->getProvider()->retrieveById($userId)
            : Auth::user();
        $instance->isUserScoped = true;

        $instance->forgetCache(self::CACHE_KEY);
    }

    public static function forgetVisitorCache(): void
    {
        $instance = new static();

        $instance->user         = null;
        $instance->isUserScoped = true;

        $instance->forgetCache(self::CACHE_KEY);
    }
}
