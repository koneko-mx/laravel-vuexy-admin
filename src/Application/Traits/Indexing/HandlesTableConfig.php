<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

/**
 * Trait para configurar la tabla Bootstrap o equivalentes.
 */
trait HandlesTableConfig
{
    public static function getIndexFormatters(): array { return []; }
    public static function getIndexPriorities(): array { return []; }
    public static function getIndexRoutes(): array { return []; }
    public static function getIndexTableConfig(): array { return []; }
    public static function getIndexFilters(): array { return []; }
    public function getSortColumn(): string { return 'id'; }
    public function getDefaultSortOrder(): string { return 'desc'; }

    public static function mergeFilters(array $builders): array
    {
        $merged = [];

        foreach ($builders as $builder) {
            if (!method_exists($builder, 'getIndexFilters')) {
                continue;
            }

            foreach ($builder::getIndexFilters() as $key => $value) {
                $merged[$key] = array_merge($merged[$key] ?? [], $value);
            }
        }

        return $merged;
    }
}
