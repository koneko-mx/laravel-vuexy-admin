<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Enums;

trait HasIconRegistry
{
    public static function getCategoryIcons(): array
    {
        return config('vuexy-warehouse.category-icons-list', []);
    }

    public static function getProductPropertyIcons(): array
    {
        return config('vuexy-warehouse.product-property-icons-list', []);
    }

    public static function getRandomIcon(string $type = 'category'): ?string
    {
        $list = $type === 'property'
            ? static::getProductPropertyIcons()
            : static::getCategoryIcons();

        return count($list) ? array_rand($list) : null;
    }
}
