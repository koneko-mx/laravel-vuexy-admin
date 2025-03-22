<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class QuickAccessWidget extends Component
{
    public $quickAccessItems = [];

    public function mount()
    {
        $menuConfig = config('vuexy_menu');
        $this->quickAccessItems = $this->processMenu($menuConfig);
    }

    private function processMenu(array $menu): array
    {
        $user = Auth::user();
        $accessItems = [];

        foreach ($menu as $section => $items) {
            if (!isset($items['submenu']) || !is_array($items['submenu'])) {
                continue;
            }

            $categoryData = [
                'title' => $section,
                'icon' => $items['icon'] ?? 'ti ti-folder',
                'description' => $items['description'] ?? '',
                'submenu' => []
            ];

            $this->processSubmenu($items['submenu'], $categoryData['submenu'], $user);

            if (!empty($categoryData['submenu'])) {
                $accessItems[] = $categoryData;
            }
        }

        return $accessItems;
    }



    private function processSubmenu(array $submenu, array &$categorySubmenu, $user)
    {
        foreach ($submenu as $title => $item) {
            // Si el elemento NO tiene 'route' ni 'url' y SOLO contiene un submenu, no lo mostramos como acceso directo
            if (!isset($item['route']) && !isset($item['url']) && isset($item['submenu'])) {
                // Procesamos los submenús de este elemento sin agregarlo directamente a la lista
                $this->processSubmenu($item['submenu'], $categorySubmenu, $user);
                continue;
            }

            // Validar si el usuario tiene permiso
            $can = $item['can'] ?? null;
            if (!$can || $user->can($can)) {
                // Si tiene ruta y existe en Laravel, usarla; si no, usar url, y si tampoco hay, usar 'javascript:;'
                $routeExists = isset($item['route']) && Route::has($item['route']);
                $url = $routeExists ? route($item['route']) : ($item['url'] ?? 'javascript:;');

                // Agregar elemento al submenu si tiene un destino válido
                $categorySubmenu[] = [
                    'title' => $title,
                    'icon' => $item['icon'] ?? 'ti ti-circle',
                    'url' => $url,
                ];
            }

            // Si el elemento tiene un submenu, también lo procesamos
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->processSubmenu($item['submenu'], $categorySubmenu, $user);
            }
        }
    }


    public function render()
    {
        return view('vuexy-admin::livewire.vuexy.quick-access-widget', [
            'quickAccessItems' => $this->quickAccessItems,
        ]);
    }
}
