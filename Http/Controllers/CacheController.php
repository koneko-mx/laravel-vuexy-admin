<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Koneko\VuexyAdmin\Services\CacheConfigService;

class CacheController extends Controller
{
    public function index(CacheConfigService $cacheConfigService)
    {
        $configCache = $cacheConfigService->getConfig();

        return view('vuexy-admin::cache-manager.index', compact('configCache'));
    }

    public function generateConfigCache()
    {
        try {
            // Lógica para generar cache
            Artisan::call('config:cache');

            return response()->json(['success' => true, 'message' => 'Cache generado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar el cache.', 'error' => $e->getMessage()], 500);
        }
    }

    public function generateRouteCache()
    {
        try {
            // Lógica para generar cache de rutas
            Artisan::call('route:cache');

            return response()->json(['success' => true, 'message' => 'Cache de rutas generado correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar el cache de rutas.', 'error' => $e->getMessage()], 500);
        }
    }
}
