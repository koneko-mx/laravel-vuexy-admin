<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

use Illuminate\Support\Str;

/**
 * Trait para construir labels legibles a partir de las columnas.
 */
trait HandlesIndexLabels
{
    public static function getIndexLabels(): array
    {
        $columns = static::getIndexColumns();
        $labels = [];

        foreach ($columns as $column) {
            if ($column instanceof \Illuminate\Database\Query\Expression) {
                $sql = $column->getValue();

                if (preg_match('/AS (\w+)/i', $sql, $matches)) {
                    $key = $matches[1];

                } else {
                    continue;
                }

            } elseif (is_string($column)) {
                if (preg_match('/\s+AS\s+(\w+)/i', $column, $matches)) {
                    $key = $matches[1];

                } elseif (str_contains($column, '.')) {
                    $key = explode('.', $column)[1];

                } else {
                    $key = $column;
                }

            } else {
                continue;
            }

            $labels[$key] = Str::headline(str_replace('_', ' ', $key));
        }

        return $labels;
    }

    public static function sortLabelsByPriority(array $labels): array
    {
        if (!method_exists(static::class, 'getIndexPriorities')) {
            return $labels;
        }

        $priorities = static::getIndexPriorities();

        uksort($labels, function ($a, $b) use ($priorities) {
            return ($priorities[$a] ?? 1000) <=> ($priorities[$b] ?? 1000);
        });

        return $labels;
    }
}
