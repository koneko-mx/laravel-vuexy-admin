<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Catalogs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use BackedEnum;

/**
 * Clase base para la consulta de catálogos reutilizable por módulos.
 *
 * Proporciona:
 * - Consulta flexible de catálogos con condiciones, ordenamiento y búsqueda.
 * - Soporte para catálogos fijos, enums y provenientes de base de datos.
 * - Formatos de salida versátiles: array, select2, raw.
 */
abstract class AbstractCatalogService
{
    /**
     * Catálogos definidos por el módulo.
     */
    private array $catalogs = [];

    /**
     * Devuelve la lista de catálogos definidos.
     */
    public function catalogs(): array
    {
        return array_keys($this->catalogs);
    }

    /**
     * Verifica si un catálogo existe.
     */
    public function exists(string $catalog): bool
    {
        return isset($this->catalogs[$catalog]);
    }

    /**
     * Consulta los datos de un catálogo, con soporte de filtros o enum.
     */
    public function getCatalog(string $catalog, string $searchTerm = '', array $options = []): array
    {
        $config = $this->catalogs[$catalog] ?? null;
        if (!$config) {
            return [];
        }

        if (isset($config['enum']) && is_subclass_of($config['enum'], BackedEnum::class)) {
            return $this->enumToCatalogArray($config['enum'], $searchTerm, $options);
        }

        $query = DB::table($config['table']);

        // Columnas a seleccionar
        $selectFields = $config['columns'] ?? [];
        if (!in_array($config['key'], $selectFields)) {
            $selectFields[] = $config['key'];
        }
        if (!in_array($config['value'], $selectFields)) {
            $selectFields[] = $config['value'];
        }

        $query->selectRaw(implode(', ', $selectFields));

        // Filtro por status si aplica
        if (($config['use_status'] ?? false) === true) {
            $query->where('status', $options['status'] ?? 'Active');
        }

        // Filtros adicionales
        foreach ($config['extra_conditions'] ?? [] as $field) {
            if (isset($options[$field])) {
                $query->where($field, $options[$field]);
            }
        }

        // Búsqueda
        if (!empty($searchTerm) && !empty($config['search_columns'])) {
            $query->where(function ($q) use ($searchTerm, $config) {
                foreach ($config['search_columns'] as $col) {
                    $q->orWhere($col, 'LIKE', "%{$searchTerm}%");
                }
            });
        }

        // Orden y límite
        $query->orderBy($config['order_by'] ?? $config['key'], 'asc');
        if (isset($config['limit'])) {
            $query->limit($config['limit']);
        }

        // Formato de salida
        $raw     = $options['rawMode'] ?? false;
        $select2 = $options['select2Mode'] ?? false;
        $key     = Str::afterLast($config['key'], '.');

        if ($raw) {
            return $query->get()->toArray();
        }

        if ($select2) {
            return $query->get()->map(fn ($row) => [
                'id'   => $row->{$key},
                'text' => $row->item,
            ])->toArray();
        }

        return $query->pluck('item', $key)->toArray();
    }

    /**
     * Devuelve los metadatos del catálogo para interfaz gráfica.
     */
    public function getCatalogMeta(string $catalog): array
    {
        $config = $this->catalogs[$catalog] ?? null;
        if (!$config) {
            return [];
        }

        return [
            'table'      => $config['table']     ?? null,
            'key'        => $config['key']       ?? null,
            'label'      => $config['value']     ?? null,
            'enum'       => $config['enum']      ?? null,
            'searchable' => $config['search_columns'] ?? [],
            'filters'    => $config['extra_conditions'] ?? [],
            'statusable'=> $config['use_status'] ?? false,
            'limit'      => $config['limit']      ?? null,
        ];
    }

    /**
     * Convierte un enum en array consultable y filtrable.
     */
    private function enumToCatalogArray(string $enumClass, string $searchTerm = '', array $options = []): array
    {
        $items = collect($enumClass::cases())
            ->mapWithKeys(fn ($case) => [$case->value => method_exists($case, 'label') ? $case->label() : $case->name]);

        if ($searchTerm) {
            $items = $items->filter(fn ($label) => str_contains(Str::lower($label), Str::lower($searchTerm)));
        }

        if ($options['select2Mode'] ?? false) {
            return $items->map(fn ($label, $key) => [
                'id' => $key,
                'text' => $label
            ])->values()->toArray();
        }

        return $items->toArray();
    }
}
