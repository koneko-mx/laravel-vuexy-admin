<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;

class CatalogAjaxController extends Controller
{
    /**
     * Consulta un catálogo de un componente vía AJAX.
     *
     * @param Request $request
     * @param string $component  Slug del componente, ej. 'sat-catalogs'
     * @param string $catalog    Nombre del catálogo, ej. 'forma_pago'
     * @return JsonResponse
     */
    public function fetch(Request $request, string $component, string $catalog): JsonResponse
    {
        $service = CatalogModuleRegistry::get($component);

        if (!$service || !$service->exists($catalog)) {
            return response()->json([
                'error' => 'Catálogo no disponible o componente no registrado.',
            ], 404);
        }

        $search  = $request->get('searchTerm', '');
        $options = $request->except(['searchTerm']);

        try {
            $results = $service->getCatalog($catalog, $search, $options);

            return response()->json($results);
        } catch (\Throwable $e) {
            report($e); // log opcional
            return response()->json([
                'error' => 'Error al consultar el catálogo.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
