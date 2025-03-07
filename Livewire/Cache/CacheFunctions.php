<?php

namespace Koneko\VuexyAdmin\Livewire\Cache;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CacheFunctions extends Component
{
    private $targetNotify = "#cache-functions-card .notification-container";

    public $cacheCounts = [
        'general' => 0,
        'config' => 0,
        'routes' => 0,
        'views' => 0,
        'events' => 0,
    ];

    protected $listeners = [
        'reloadCacheFunctionsStatsEvent' => 'reloadCacheStats',
    ];

    public function mount()
    {
        $this->reloadCacheStats(false);
    }

    public function reloadCacheStats($notify = true)
    {
        $cacheDriver = config('cache.default'); // Obtiene el driver configurado para caché

        // Caché General
        switch ($cacheDriver) {
            case 'memcached':
                try {
                    $cacheStore = Cache::getStore()->getMemcached();
                    $stats = $cacheStore->getStats();

                    $this->cacheCounts['general'] = array_sum(array_column($stats, 'curr_items')); // Total de claves en Memcached
                } catch (\Exception $e) {
                    $this->cacheCounts['general'] = 'Error obteniendo datos de Memcached';
                }
                break;

            case 'redis':
                try {
                    $prefix = config('cache.prefix'); // Asegúrate de agregar el sufijo correcto si es necesario
                    $keys = Redis::connection('cache')->keys($prefix . '*');

                    $this->cacheCounts['general'] = count($keys); // Total de claves en Redis
                } catch (\Exception $e) {
                    $this->cacheCounts['general'] = 'Error obteniendo datos de Redis';
                }
                break;

            case 'database':
                try {
                    $this->cacheCounts['general'] = DB::table('cache')->count(); // Total de registros en la tabla de caché
                } catch (\Exception $e) {
                    $this->cacheCounts['general'] = 'Error obteniendo datos de la base de datos';
                }
                break;

            case 'file':
                try {
                    $cachePath = config('cache.stores.file.path');
                    $files = glob($cachePath . '/*');

                    $this->cacheCounts['general'] = count($files);
                } catch (\Exception $e) {
                    $this->cacheCounts['general'] = 'Error obteniendo datos de archivos';
                }
                break;

            default:
                $this->cacheCounts['general'] = 'Driver de caché no soportado';
        }

        // Configuración
        $this->cacheCounts['config'] = file_exists(base_path('bootstrap/cache/config.php')) ? 1 : 0;

        // Rutas
        $this->cacheCounts['routes'] = count(glob(base_path('bootstrap/cache/routes-*.php'))) > 0 ? 1 : 0;

        // Vistas
        $this->cacheCounts['views'] = count(glob(storage_path('framework/views/*')));

        // Configuración
        $this->cacheCounts['events'] = file_exists(base_path('bootstrap/cache/events.php')) ? 1 : 0;

        if ($notify) {
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: 'success',
                message: 'Se han recargado los estadísticos de caché.'
            );
        }
    }


    public function clearLaravelCache()
    {
        Artisan::call('cache:clear');

        sleep(1);

        $this->response('Se han limpiado las cachés de la aplicación.', 'warning');
    }

    public function clearConfigCache()
    {
        Artisan::call('config:clear');

        $this->response('Se ha limpiado la cache de la configuración de Laravel.', 'warning');
    }

    public function configCache()
    {
        Artisan::call('config:cache');
    }

    public function clearRouteCache()
    {
        Artisan::call('route:clear');

        $this->response('Se han limpiado las rutas de Laravel.', 'warning');
    }

    public function cacheRoutes()
    {
        Artisan::call('route:cache');
    }

    public function clearViewCache()
    {
        Artisan::call('view:clear');

        $this->response('Se han limpiado las vistas de Laravel.', 'warning');
    }

    public function cacheViews()
    {
        Artisan::call('view:cache');

        $this->response('Se han cacheado las vistas de Laravel.');
    }

    public function clearEventCache()
    {
        Artisan::call('event:clear');

        $this->response('Se han limpiado los eventos de Laravel.', 'warning');
    }

    public function cacheEvents()
    {
        Artisan::call('event:cache');

        $this->response('Se han cacheado los eventos de Laravel.');
    }

    public function optimizeClear()
    {
        Artisan::call('optimize:clear');

        $this->response('Se han optimizado todos los cachés de Laravel.');
    }

    public function resetPermissionCache()
    {
        Artisan::call('permission:cache-reset');

        $this->response('Se han limpiado los cachés de permisos.', 'warning');
    }

    public function clearResetTokens()
    {
        Artisan::call('auth:clear-resets');

        $this->response('Se han limpiado los tokens de reseteo de contraseña.', 'warning');
    }

    /**
     * Genera una respuesta estandarizada.
     */
    private function response(string $message, string $type = 'success'): void
    {
        $this->reloadCacheStats(false);

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: $type,
            message: $message,
        );

        $this->dispatch('reloadCacheStatsEvent', notify: false);
        $this->dispatch('reloadSessionStatsEvent', notify: false);
        $this->dispatch('reloadRedisStatsEvent', notify: false);
        $this->dispatch('reloadMemcachedStatsEvent', notify: false);
    }

    public function render()
    {
        return view('vuexy-admin::livewire.cache.cache-functions');
    }
}
