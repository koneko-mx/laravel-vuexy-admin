<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

/**
 * Trait para definir las columnas del índice.
 */
trait HandlesIndexColumns
{
    abstract public static function getIndexColumns(): array;
    public static function getIndexJoins(): array { return []; }
    public static function getIndexGroupBy(): array|false { return false; }

    public static function uniqueJoins(array $joins): array
    {
        $seen = [];
        $result = [];

        foreach ($joins as $join) {
            $key = is_array($join) ? json_encode($join) : $join;

            if (!isset($seen[$key])) {
                $result[] = $join;
                $seen[$key] = true;
            }
        }

        return $result;
    }

}
