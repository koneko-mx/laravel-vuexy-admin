<?php

namespace Koneko\VuexyAdmin\Application\UX\Breadcrumbs;

use Illuminate\Support\Facades\View;

final class Breadcrumbs
{
    public static function current(): array
    {
        return View::shared('vuexyBreadcrumbs') ?? [];
    }

    public static function extend(array $items): array
    {
        $trail = self::current();

        // 1) reset active
        foreach ($trail as &$b) { $b['active'] = false; }

        // 2) normaliza items
        $normalized = array_map(function ($i) {
            return [
                'name'   => $i['name'],
                'link'   => $i['link']   ?? null,
                'active' => (bool)($i['active'] ?? false),
            ];
        }, $items);

        // 3) merge
        $trail = array_merge($trail, $normalized);

        // 4) asegurar último activo
        if (!empty($trail)) {
            $trail[array_key_last($trail)]['active'] = true;
        }

        View::share('vuexyBreadcrumbs', $trail);
        return $trail;
    }

    public static function push(string $name, ?string $link = null, bool $active = false): array
    {
        return self::extend([compact('name', 'link', 'active')]);
    }
}
