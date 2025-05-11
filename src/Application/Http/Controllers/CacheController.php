<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Koneko\VuexyAdmin\Application\Cache\CacheConfigService;

class CacheController extends Controller
{
    public function laravelIndex(): View
    {
        return view('vuexy-admin::tools.cache.laravel.index');
    }

    public function redisIndex(CacheConfigService $cacheConfigService): View
    {
        $configCache = $cacheConfigService->getConfig();

        return view('vuexy-admin::tools.cache.redis.index', compact('configCache'));
    }

    public function memcacheIndex(CacheConfigService $cacheConfigService): View
    {
        $configCache = $cacheConfigService->getConfig();

        return view('vuexy-admin::tools.cache.memcache.index', compact('configCache'));
    }

    public function generateConfigCache(): JsonResponse
    {
        try {
            // Lógica para generar cache
            Artisan::call('config:cache');

            return response()->json(['success' => true, 'message' => 'Cache generado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar el cache.', 'error' => $e->getMessage()], 500);
        }
    }

    public function generateRouteCache(): JsonResponse
    {
        try {
            // Lógica para generar cache de rutas
            Artisan::call('route:cache');

            return response()->json(['success' => true, 'message' => 'Cache de rutas generado correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar el cache de rutas.', 'error' => $e->getMessage()], 500);
        }
    }

    public function sessionsIndex(): View
    {
        return view('vuexy-admin::tools.cache.sessions.index');
    }

    public function generateViteAssetsCache(): View
    {
        return view('vuexy-admin::tools.cache.vite-assets.index');
    }

    public function generateTtlCache(): View
    {
        return view('vuexy-admin::tools.cache.ttl.index');
    }
}
