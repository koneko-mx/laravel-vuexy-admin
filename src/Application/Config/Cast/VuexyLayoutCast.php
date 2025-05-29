<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Config\Cast;

class VuexyLayoutCast
{
    public function cast(mixed $value, string $key): mixed
    {
        return match (true) {
            in_array($key, ['hasCustomizer', 'displayCustomizer', 'footerFixed', 'menuFixed', 'menuCollapsed', 'showDropdownOnHover']) => (bool) $value,
            $key === 'maxQuickLinks' => (int) $value,
            default => $value,
        };
    }
}
