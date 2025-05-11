<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Pages\Dashboards;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;

/**
 * 🎛️ Componente para mostrar accesos rápidos (Inicio o Carpeta del menú).
 *
 * Soporta:
 * - `slug`: ruta amigable de carpeta.
 * - `nodeId`: ID del nodo (opcional).
 *
 * Muestra tarjetas agrupadas (`home_at_root`) o planas.
 */
class MenuAccessCards extends Component
{
    public ?string $slug = null;
    public ?int $nodeId  = null;

    public array $components = [];
    public array $menuCards  = [];

    public string $title       = 'Inicio';
    public string $description = '';
    public string $icon        = 'ti ti-home';

    public function mount(): void
    {
        if ($this->slug) {
            $entry = app(VuexyMenuFormatter::class)->getMenuBySlug($this->slug);

echo '<pre>';
            print_r($entry);
echo '</pre>';


            if ($entry) {
                $key   = array_key_first($entry);
                $node  = $entry[$key];
                $trail = $node['_trail'] ?? [];

                $this->title       = $trail[array_key_last($trail)]['label'] ?? $key;
                $this->description = $node['description'] ?? ($node['_meta']['description'] ?? '');
                $this->icon        = $node['icon'] ?? ($node['_meta']['icon'] ?? 'ti ti-folder');

                $submenu = $node['submenu'] ?? [];
                $layout  = $node['_meta']['home_layout'] ?? 'flat';

                $this->menuCards = match ($layout) {
                    'grouped' => $this->buildGroupedCards($submenu),
                    default   => $this->buildFlatCards($submenu),
                };

                $this->components = $this->extractComponentsFromRegistry($this->menuCards);
                return;
            }
        }

        $menu = app(VuexyMenuFormatter::class)->getMenu();

        $this->menuCards  = $this->buildGroupedCards($menu);
        $this->components = $this->extractComponentsFromRegistry($this->menuCards);
    }

    protected function extractComponentsFromRegistry(): array
    {
        $usedComponents = collect($this->menuCards)
            ->flatMap(fn($group) => $group['cards'] ?? [])
            ->pluck('component')
            ->filter()
            ->unique()
            ->values();

        return collect(KonekoModuleRegistry::enabled())
            ->filter(fn($module) => $usedComponents->contains($module->componentNamespace))
            ->map(fn($module) => [
                'component' => $module->componentNamespace,
                'label'     => $module->name,
            ])
            ->sortBy(function ($item) {
                return $item['component'] === 'core' ? -1 : $item['label'];
            })
            ->values()
            ->toArray();
    }

    /*
    protected function hasHomeAtRoot(array $submenu): bool
    {
        return collect($submenu)
            ->filter(fn($item) => !empty($item['_meta']['home_at_root']))
            ->count() > 1;
    }
    */

    protected function buildGroupedCards(array $menu): array
    {
        $roots = $this->extractRootElements($menu);
        $cards = $this->collectCards($menu);

        $excludedAutoIds = $this->getAutoIdsFromRootGroups($roots, $cards);

        $mainGroup = [
            'type'        => 'root',
            'title'       => $this->title,
            'icon'        => $this->icon,
            'description' => $this->description,
            'cards'       => collect($cards)
                ->reject(fn($c) => in_array($c['auto_id'], $excludedAutoIds))
                ->unique('auto_id')
                ->values()
                ->map(fn($c) => Arr::only($c, ['title', 'icon', 'url', '_target', 'disallowed_link', 'component', 'hidden_item']))
                ->toArray(),
        ];


        // 2. Agregamos los grupos secundarios como "Configuraciones de contactos"
        $subGroups = collect($roots)->map(function ($root) use ($cards) {
            $grouped = collect($cards)
                ->filter(fn($c) => $c['category'] === $root['title'])
                ->unique('auto_id')
                ->values()
                ->map(fn($c) => Arr::only($c, ['title', 'icon', 'url', '_target', 'disallowed_link', 'component', 'hidden_item']))
                ->toArray();

            return array_merge($root, ['cards' => $grouped]);
        })->toArray();

        return array_merge([$mainGroup], $subGroups);
    }


    protected function buildFlatCards(array $submenu): array
    {
        $cards = $this->collectCards($submenu);

        return [[
            'type'        => 'root',
            'title'       => $this->title,
            'icon'        => $this->icon,
            'description' => $this->description,
            'cards'       => collect($cards)
                                ->unique('auto_id')
                                ->values()
                                ->map(fn($c) => Arr::only($c, ['title', 'icon', 'url', '_target', 'disallowed_link', 'component', 'hidden_item']))
                                ->toArray(),
        ]];
    }

    protected function extractRootElements(array $menu): array
    {
        return $this->collectPromotedRootsRecursive($menu);
    }

    protected function collectPromotedRootsRecursive(array $submenu, array &$roots = []): array
    {
        foreach ($submenu as $key => $item) {
            if (!empty($item['_meta']['home_at_root'])) {
                $roots[] = $this->formatAsRootItem($key, $item);
            }

            if (!empty($item['submenu']) && is_array($item['submenu'])) {
                $this->collectPromotedRootsRecursive($item['submenu'], $roots);
            }
        }

        return $roots;
    }


    protected function collectPromotedRoots(array $submenu, array &$roots): void
    {
        foreach ($submenu as $key => $item) {
            if (!empty($item['_meta']['home_at_root'])) {
                $roots[] = $this->formatAsRootItem($key, $item);
            }

            if (!empty($item['submenu'])) {
                $this->collectPromotedRoots($item['submenu'], $roots);
            }
        }
    }

    protected function collectCards(array $menu, array &$collector = [], ?string $category = null): array
    {
        foreach ($menu as $key => $item) {
            $label = $item['_meta']['widget_label'] ?? $item['_meta']['original_key'] ?? $key;

            // Este es el nombre que define el grupo real
            $groupName = !empty($item['_meta']['home_at_root']) ? $label : ($category ?? $label);


            if (!empty($item['route']) || !empty($item['url'])) {
                $colection = [
                    'auto_id'  => $item['_meta']['auto_id'] ?? crc32($key . ($item['route'] ?? $item['url'] ?? '')),
                    'title'    => $label,
                    'icon'     => $item['icon'] ?? 'ti ti-circle',
                    'url'      => isset($item['route']) && Route::has($item['route'])
                                    ? route($item['route'])
                                    : ($item['url'] ?? 'javascript:;'),
                    '_target'  => (isset($item['url']) && str_starts_with($item['url'], 'http')) ? '_blank' : null,
                    'component' => isset($item['_meta']['component'])? $item['_meta']['component']: null,
                    'category' => $groupName,
                ];

                if(config('koneko.admin.menu.debug.show_disallowed_links')){
                    $colection['disallowed_link'] = $item['_meta']['disallowed_link'];
                }

                if(config('koneko.admin.menu.debug.show_hidden_items')){
                    $colection['hidden_item'] = $item['_meta']['hidden_item'];
                }

                $collector[] = $colection;
            }

            if (!empty($item['submenu'])) {
                $this->collectCards($item['submenu'], $collector, $groupName);
            }
        }

        return $collector;
    }

    protected function formatAsRootItem(string $key, array $item): array
    {
        return [
            'type'        => 'root',
            'title'       => $item['_meta']['widget_label'] ?? $item['_meta']['original_key'] ?? $key,
            'icon'        => $item['icon'] ?? 'ti ti-folder',
            'description' => $item['description'] ?? '',
        ];
    }

    /*
    protected function shouldGroupSubmenu(array $submenu): bool
    {
        $homeAtRoot = collect($submenu)
            ->filter(fn($item) => !empty($item['_meta']['home_at_root']));

        // Si hay más de uno, agrupamos
        if ($homeAtRoot->count() > 1) return true;

        // Si solo hay uno con submenu, agrupamos
        if ($homeAtRoot->count() === 1 && !empty($homeAtRoot->first()['submenu'])) {
            return true;
        }

        // Si no hay ninguno o es plano, NO agrupamos
        return false;
    }
    */

    protected function getAutoIdsFromRootGroups(array $roots, array $cards): array
    {
        return collect($roots)
            ->flatMap(function ($root) use ($cards) {
                return collect($cards)
                    ->filter(fn($c) => $c['category'] === $root['title'])
                    ->pluck('auto_id');
            })
            ->unique()
            ->values()
            ->toArray();
    }


    public function render()
    {
        return view('vuexy-admin::livewire.pages.dashboard.menu-access-cards', [
            'menuCards' => $this->menuCards,
        ]);
    }
}
