<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Breadcrumbs;

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;

class VuexyBreadcrumbsBuilder
{
    private array $menu;

    public function __construct(?array $menu = null)
    {
        // Permitir inyectar un menú preprocesado o usar el Builder
        $this->menu = $menu ?? app(VuexyMenuFormatter::class)->getMenu();
    }

    /**
     * Devuelve el trail de breadcrumbs con "Inicio" siempre al inicio
     */
    public function getBreadcrumbs(): array
    {
        // Si estamos en la página de inicio, no mostrar breadcrumbs
        if (Route::currentRouteName() === 'admin.core.pages.home.index') {
            return []; //  Esto hará que $vuexyBreadcrumbs sea falsy y no se renderice el <nav>
        }

        $breadcrumbs = $this->findBreadcrumbTrail($this->menu);

        // Asegura "Inicio" al principio
        array_unshift($breadcrumbs, [
            'name' => 'Inicio',
            'link' => route('admin.core.pages.home.index'),
            'active' => false,
        ]);

        // Marca el último breadcrumb como activo
        if (!empty($breadcrumbs)) {
            $breadcrumbs[array_key_last($breadcrumbs)]['active'] = true;
        }

        return $breadcrumbs;
    }

    private function findBreadcrumbTrail(array $menu, array $trail = []): array
    {
        $currentRoute = Route::currentRouteName();
        $currentSlug  = $this->getCurrentSlug();

        foreach ($menu as $title => $item) {
            $skip = $item['breadcrumbs'] ?? false;

            // Verificación por route
            $routeMatches = isset($item['route']) && $item['route'] === $currentRoute;
            $partialMatch = isset($item['route']) && str_starts_with($currentRoute, dirname($item['route']));
            $slugMatches  = $currentSlug && isset($item['_slug']) && $item['_slug'] === $currentSlug;

            $newTrail = $trail;

            // Si se va a agregar al trail visual (breadcrumb)
            $isBreadcrumbNode = !$skip;

            // Verificamos si es el "Inicio" duplicado
            $isDuplicateInicio = strtolower($title) === 'inicio' &&
                (
                    ($item['route'] ?? null) === 'admin.core.pages.home.index' ||
                    ($item['_slug'] ?? null) === 'inicio'
                );

            if ($isBreadcrumbNode && !$isDuplicateInicio) {
                $newTrail[] = [
                    'name'  => $title,
                    'link'  => isset($item['route']) && route::has($item['route']) ? route($item['route']) :
                               (isset($item['_slug']) ? route('admin.core.pages.folder.view', ['slug' => $item['_slug']]) : null),
                    'active' => false,
                ];
            }

            if ($routeMatches || $partialMatch || $slugMatches) {
                return $newTrail;
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $found = $this->findBreadcrumbTrail($item['submenu'], $newTrail);
                if (!empty($found)) {
                    return $found;
                }
            }
        }


        return [];
    }

    private function getCurrentSlug(): ?string
    {
        $route = Route::current();

        if ($route && $route->getName() === 'admin.core.pages.folder.view') {
            return $route->parameter('slug');
        }

        return null;
    }
}
