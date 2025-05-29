<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model\ModelExtensionRegistry;

trait HandlesStaticRegistryMerge
{
    /**
     * Fusiona arrays de configuración de todos los módulos registrados.
     * Utilizable en contextos estáticos.
     */
    public static function mergeStaticRegistry(string $key, array $base): array
    {
        $builderClass = static::class;
        $modelClass = method_exists(static::class, 'getModelClass')
            ? (new static())->getModelClass()
            : null;

        $extensions = [];

        // 1. Extensiones registradas por modelo (si se detecta)
        if ($modelClass) {
            $extensions = array_merge(
                $extensions,
                ModelExtensionRegistry::getConfigExtensionsFor($modelClass)
            );
        }

        // 2. Extensiones registradas por builder
        $extensions = array_merge(
            $extensions,
            ModelExtensionRegistry::getConfigExtensionsFor($builderClass)
        );

        foreach ($extensions as $extensionClass) {
            if (!class_exists($extensionClass) || !method_exists($extensionClass, 'config')) {
                continue;
            }

            $config = $extensionClass::config();
            if (isset($config[$key]) && is_array($config[$key])) {
                $base = array_merge($base, $config[$key]);
            }
        }

        return $base;
    }

    /**
     * Combina filtros de múltiples módulos manteniendo claves únicas.
     * ['search' => [...], 'status' => [...]]
     */
    public static function mergeStaticFilters(array $filters): array
    {
        $merged = [];

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $merged[$key] = array_merge($merged[$key] ?? [], $value);
            }
        }

        return $merged;
    }

    /**
     * Elimina joins duplicados usando hash de JSON para comparación.
     */
    public static function uniqueJoins(array $joins): array
    {
        $seen = [];
        $unique = [];

        foreach ($joins as $join) {
            $key = is_array($join) ? json_encode($join) : $join;

            if (!isset($seen[$key])) {
                $unique[] = $join;
                $seen[$key] = true;
            }
        }

        return $unique;
    }

    /**
     * Ordena etiquetas por prioridad si el método `getIndexPriorities()` existe.
     */
    public static function sortLabelsByPriority(array $labels): array
    {
        if (!method_exists(static::class, 'getIndexPriorities')) {
            return $labels;
        }

        $priorities = static::getIndexPriorities();

        uksort($labels, fn($a, $b) => ($priorities[$a] ?? 1000) <=> ($priorities[$b] ?? 1000));

        return $labels;
    }
}
