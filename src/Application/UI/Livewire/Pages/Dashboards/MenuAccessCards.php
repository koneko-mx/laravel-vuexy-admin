<?php

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Pages\Dashboards;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Livewire\Component;

class MenuAccessCards extends Component
{
    public string $pageTitle = 'Accesos rápidos';
    public ?string $slug     = null;

    public array $groups     = [];
    public array $components = [];


    public function mount(): void
    {
        $formatter = app(VuexyMenuFormatter::class);

        if ($this->slug) {
            $entry = $formatter->getMenuBySlug($this->slug);
            $this->components = $this->extractComponentsFromRegistry($entry);
            $this->pageTitle  = Str::headline($this->slug) . ' | Accesos rápidos';

        } else {
            $entry = $formatter->getMenu();
            $this->components = $this->getEnabledModulesWithProject()->values()->toArray();
        }

        $this->groups = $this->buildGroups($entry);
    }

    protected function extractComponentsFromRegistry(array $entry): array
    {
        $components = $this->collectUsedComponents($entry);

        // Añadimos manualmente 'project' si está presente en el árbol
        if (in_array('project', $components, true)) {
            $components[] = 'project';
        }

        // Devolvemos solo los módulos habilitados que estén en el árbol de menú
        return $this->getEnabledModulesWithProject()
            ->filter(fn($item) => in_array($item['tag'], $components, true))
            ->values()
            ->toArray();
    }

    protected function collectUsedComponents(array $menu): array
    {
        $tags = [];

        $walker = function ($items) use (&$walker, &$tags) {
            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $component = $item['_meta']['component'] ?? null;
                if ($component) {
                    $tags[] = $component;
                }

                if (!empty($item['submenu']) && is_array($item['submenu'])) {
                    $walker($item['submenu']);
                }
            }
        };

        $walker($menu);

        return array_unique($tags);
    }

    protected function getEnabledModulesWithProject(): Collection
    {
        return collect(KonekoModuleRegistry::enabled())
            ->map(fn($module) => [
                'tag'   => $module->componentNamespace,
                'label' => $module->name,
            ])
            ->push([
                'tag'   => 'project',
                'label' => config('koneko.admin.project_label', 'Proyecto'),
            ])
            ->sortBy(fn($item) => $item['tag'] === 'core' ? -1 : $item['label']);
    }

    protected function buildGroups(array $menu): array
    {
        $groups = [];

        foreach ($menu as $label => $item) {
            $meta = $item['_meta'] ?? [];
            $icon = $meta['icon'] ?? $item['icon'] ?? 'ti ti-folder';
            $description = $meta['description'] ?? $item['description'] ?? null;

            $cards = [];
            $subGroups = [];

            if (!empty($item['submenu'])) {
                foreach ($item['submenu'] as $subTitle => $subItem) {
                    if (isset($subItem['url'])) {
                        $cards[] = [
                            'title'           => $subTitle,
                            'url'             => $subItem['url'],
                            'icon'            => $subItem['icon'] ?? 'ti ti-circle',
                            'disallowed_link' => $subItem['_meta']['disallowed_link'] ?? false,
                            'hidden_item'     => $subItem['_meta']['hidden_item'] ?? false,
                            'component'       => $subItem['_meta']['component'] ?? null,
                        ];
                    }

                    if (!empty($subItem['submenu'])) {
                        $subGroups = array_merge($subGroups, $this->buildGroups([$subTitle => $subItem]));
                    }
                }

            } elseif (isset($item['url'])) {
                $cards[] = [
                    'label'           => $label,
                    'url'             => $item['url'],
                    'icon'            => $item['icon'] ?? 'ti ti-circle',
                    'disallowed_link' => $item['_meta']['disallowed_link'] ?? false,
                    'hidden_item'     => $item['_meta']['hidden_item'] ?? false,
                    'component'       => $item['_meta']['component'] ?? null,
                ];
            }

            if (!empty($cards)) {
                $groups[] = [
                    'label'       => $label,
                    'icon'        => $icon,
                    'description' => $description,
                    'cards'       => $cards,
                ];
            }

            if (!empty($subGroups)) {
                $groups = array_merge($groups, $subGroups);
            }
        }

        return $groups;
    }

    public function render()
    {
        return view('vuexy-admin::livewire.pages.dashboard.menu-access-cards');
    }
}
