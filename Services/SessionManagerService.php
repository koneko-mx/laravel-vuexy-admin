<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SessionManagerService
{
    private string $driver;

    public function __construct(string $driver = null)
    {
        $this->driver = $driver ?? config('session.driver');
    }

    public function getSessionStats(string $driver = null): array
    {
        $driver = $driver ?? $this->driver;

        if (!$this->isSupportedDriver($driver))
            return $this->response('warning', 'Driver no soportado o no configurado.', ['session_count' => 0]);

        try {
            switch ($driver) {
                case 'redis':
                    return $this->getRedisStats();

                case 'database':
                    return $this->getDatabaseStats();

                case 'file':
                    return $this->getFileStats();

                default:
                    return $this->response('warning', 'Driver no reconocido.', ['session_count' => 0]);
            }
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas: ' . $e->getMessage(), ['session_count' => 0]);
        }
    }

    public function clearSessions(string $driver = null): array
    {
        $driver = $driver ?? $this->driver;

        if (!$this->isSupportedDriver($driver)) {
            return $this->response('warning', 'Driver no soportado o no configurado.');
        }

        try {
            switch ($driver) {
                case 'redis':
                    return $this->clearRedisSessions();

                case 'memcached':
                    Cache::getStore()->flush();
                    return $this->response('success', 'Se eliminó la memoria caché de sesiones en Memcached.');

                case 'database':
                    DB::table('sessions')->truncate();
                    return $this->response('success', 'Se eliminó la memoria caché de sesiones en la base de datos.');

                case 'file':
                    return $this->clearFileSessions();

                default:
                    return $this->response('warning', 'Driver no reconocido.');
            }
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al limpiar las sesiones: ' . $e->getMessage());
        }
    }


    private function getRedisStats()
    {
        $prefix = config('cache.prefix'); // Asegúrate de agregar el sufijo correcto si es necesario
        $keys = Redis::connection('sessions')->keys($prefix . '*');

        return $this->response('success', 'Se ha recargado la información de la caché de Redis.', ['session_count' => count($keys)]);
    }

    private function getDatabaseStats(): array
    {
        $sessionCount = DB::table('sessions')->count();

        return $this->response('success', 'Se ha recargado la información de la base de datos.', ['session_count' => $sessionCount]);
    }

    private function getFileStats(): array
    {
        $cachePath = config('session.files');
        $files = glob($cachePath . '/*');

        return $this->response('success', 'Se ha recargado la información de sesiones de archivos.', ['session_count' => count($files)]);
    }


    /**
     * Limpia sesiones en Redis.
     */
    private function clearRedisSessions(): array
    {
        $prefix = config('cache.prefix', '');
        $keys = Redis::connection('sessions')->keys($prefix . '*');

        if (!empty($keys)) {
            Redis::connection('sessions')->flushdb();

            // Simulate cache clearing delay
            sleep(1);

            return $this->response('success', 'Se eliminó la memoria caché de sesiones en Redis.');
        }

        return $this->response('info', 'No se encontraron claves para eliminar en Redis.');
    }

    /**
     * Limpia sesiones en archivos.
     */
    private function clearFileSessions(): array
    {
        $cachePath = config('session.files');
        $files = glob($cachePath . '/*');

        if (!empty($files)) {
            foreach ($files as $file) {
                unlink($file);
            }

            return $this->response('success', 'Se eliminó la memoria caché de sesiones en archivos.');
        }

        return $this->response('info', 'No se encontraron sesiones en archivos para eliminar.');
    }


    private function isSupportedDriver(string $driver): bool
    {
        return in_array($driver, ['redis', 'memcached', 'database', 'file']);
    }

    /**
     * Genera una respuesta estandarizada.
     */
    private function response(string $status, string $message, array $data = []): array
    {
        return array_merge(compact('status', 'message'), $data);
    }
}
