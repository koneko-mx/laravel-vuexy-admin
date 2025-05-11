<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class KonekoCatalogHelper
{
    public static function ajaxFlexibleResponse(Builder $query, array $options = []): JsonResponse
    {
        $id = $options['id'] ?? null;
        $searchTerm = $options['searchTerm'] ?? null;
        $limit = $options['limit'] ?? 20;
        $keyField = $options['keyField'] ?? 'id';
        $valueField = $options['valueField'] ?? 'name';
        $responseType = $options['responseType'] ?? 'select2';
        $customFilters = $options['filters'] ?? [];

        // Si se pasa un ID, devolver un registro completo
        if ($id) {
            $data = $query->find($id);
            return response()->json($data);
        }

        // Aplicar filtros personalizados
        foreach ($customFilters as $field => $value) {
            if (!is_null($value)) {
                $query->where($field, $value);
            }
        }

        // Aplicar filtro de búsqueda si hay searchTerm
        if ($searchTerm) {
            $query->where($valueField, 'like', '%' . $searchTerm . '%');
        }

        // Limitar resultados si el límite no es falso
        if ($limit > 0) {
            $query->limit($limit);
        }

        $results = $query->get([$keyField, $valueField]);

        // Devolver según el tipo de respuesta
        switch ($responseType) {
            case 'keyValue':
                $data = $results->pluck($valueField, $keyField)->toArray();
                break;

            case 'select2':
                $data = $results->map(function ($item) use ($keyField, $valueField) {
                    return [
                        'id' => $item->{$keyField},
                        'text' => $item->{$valueField},
                    ];
                })->toArray();
                break;

            default:
                $data = $results->map(function ($item) use ($keyField, $valueField) {
                    return [
                        'id' => $item->{$keyField},
                        'text' => $item->{$valueField},
                    ];
                })->toArray();
                break;
        }

        return response()->json($data);
    }
}
