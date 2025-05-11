<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Cache;

use Illuminate\Support\Facades\{Cache,DB,Redis,File};
use Memcached;

/**
 * Servicio para gestionar y administrar el sistema de caché.
 *
 * Esta clase proporciona funcionalidades para administrar diferentes drivers de caché
 * (Redis, Memcached, Database, File), incluyendo operaciones como obtener estadísticas,
 * limpiar la caché y monitorear el uso de recursos.
 */
class LaravelCacheManager
{
    /** @var string Driver de caché actualmente seleccionado */
    private string $driver;

    /**
     * Constructor del servicio de gestión de caché.
     *
     * @param mixed $driver Driver de caché a utilizar. Si es null, se usa el driver predeterminado
     */
    public function __construct(mixed $driver = null)
    {
        $this->driver = $driver ?? config('cache.default');
    }

    /**
     * Obtiene estadísticas de caché para el driver especificado.
     *
     * Recopila información detallada sobre el uso y rendimiento del sistema de caché,
     * incluyendo uso de memoria, número de elementos y estadísticas específicas del driver.
     *
     * @param mixed $driver Driver de caché del cual obtener estadísticas
     * @return array Estadísticas del sistema de caché
     */
    public function getCacheStats(mixed $driver = null): array
    {
        $driver = $driver ?? $this->driver;

        if (!$this->isSupportedDriver($driver)) {
            return $this->response('warning', 'Driver no soportado o no configurado.');
        }

        try {
            return match ($driver) {
                'database' => $this->_getDatabaseStats(),
                'file' => $this->_getFilecacheStats(),
                'redis' => $this->_getRedisStats(),
                'memcached' => $this->_getMemcachedStats(),
                default => $this->response('info', 'No hay estadísticas disponibles para este driver.'),
            };
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Limpia la caché del driver especificado.
     *
     * @param mixed $driver Driver de caché a limpiar
     * @return array Resultado de la operación de limpieza
     */
    public function clearCache(mixed $driver = null): array
    {
        $driver = $driver ?? $this->driver;

        if (!$this->isSupportedDriver($driver)) {
            return $this->response('warning', 'Driver no soportado o no configurado.');
        }

        try {
            switch ($driver) {
                case 'redis':
                    $keysCleared = $this->clearRedisCache();

                    return $keysCleared
                        ? $this->response('warning', 'Se ha purgado toda la caché de Redis.')
                        : $this->response('info', 'No se encontraron claves en Redis para eliminar.');

                case 'memcached':
                    $keysCleared = $this->clearMemcachedCache();

                    return $keysCleared
                        ? $this->response('warning', 'Se ha purgado toda la caché de Memcached.')
                        : $this->response('info', 'No se encontraron claves en Memcached para eliminar.');

                case 'database':
                    $rowsDeleted = $this->clearDatabaseCache();

                    return $rowsDeleted
                        ? $this->response('warning', 'Se ha purgado toda la caché almacenada en la base de datos.')
                        : $this->response('info', 'No se encontraron registros en la caché de la base de datos.');

                case 'file':
                    $filesDeleted = $this->clearFilecache();

                    return $filesDeleted
                        ? $this->response('warning', 'Se ha purgado toda la caché de archivos.')
                        : $this->response('info', 'No se encontraron archivos en la caché para eliminar.');

                default:
                    Cache::flush();

                    return $this->response('warning', 'Caché purgada.');
            }
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al limpiar la caché: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas detalladas del servidor Redis.
     *
     * @return array Información detallada del servidor Redis incluyendo versión, memoria, clientes y más
     */
    public function getRedisStats()
    {
        try {
            if (!Redis::ping()) {
                return $this->response('warning', 'No se puede conectar con el servidor Redis.');
            }

            $info = Redis::info();

            $databases = $this->getRedisDatabases();

            $redisInfo = [
                'server' => config('database.redis.default.host'),
                'redis_version' => $info['redis_version'] ?? 'N/A',
                'os' => $info['os'] ?? 'N/A',
                'tcp_port' => $info['tcp_port'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 'N/A',
                'blocked_clients' => $info['blocked_clients'] ?? 'N/A',
                'maxmemory' => $info['maxmemory'] ?? 0,
                'used_memory_human' => $info['used_memory_human'] ?? 'N/A',
                'used_memory_peak' => $info['used_memory_peak'] ?? 'N/A',
                'used_memory_peak_human' => $info['used_memory_peak_human'] ?? 'N/A',
                'total_system_memory' => $info['total_system_memory'] ?? 0,
                'total_system_memory_human' => $info['total_system_memory_human'] ?? 'N/A',
                'maxmemory_human' => $info['maxmemory_human'] !== '0B' ? $info['maxmemory_human'] : 'Sin Límite',
                'total_connections_received' => number_format($info['total_connections_received']) ?? 'N/A',
                'total_commands_processed' => number_format($info['total_commands_processed']) ?? 'N/A',
                'maxmemory_policy' => $info['maxmemory_policy'] ?? 'N/A',
                'role' => $info['role'] ?? 'N/A',
                'cache_database' => '',
                'sessions_database' => '',
                'general_database' => ',',
                'keys' => $databases['total_keys'],
                'used_memory' => $info['used_memory'] ?? 0,
                'uptime' => gmdate('H\h i\m s\s', $info['uptime_in_seconds'] ?? 0),
                'databases' => $databases,
            ];

            return $this->response('success', 'Se a recargado las estadísticas de Redis.', ['info' => $redisInfo]);
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al conectar con el servidor Redis: ' . Redis::getLastError());
        }
    }

    /**
     * Obtiene estadísticas detalladas del servidor Memcached.
     *
     * @return array Información detallada del servidor Memcached incluyendo versión, memoria y estadísticas de uso
     */
    public function getMemcachedStats()
    {
        try {
            $memcachedStats = [];

            // Crear instancia del cliente Memcached
            $memcached = new Memcached();
            $memcached->addServer(config('memcached.host'), config('memcached.port'));

            // Obtener estadísticas del servidor
            $stats = $memcached->getStats();

            foreach ($stats as $server => $data) {
                $server = explode(':', $server);

                $memcachedStats[] = [
                    'server' => $server[0],
                    'tcp_port' => $server[1],
                    'uptime' => $data['uptime'] ?? 'N/A',
                    'version' => $data['version'] ?? 'N/A',
                    'libevent' => $data['libevent'] ?? 'N/A',
                    'max_connections' => $data['max_connections'] ?? 0,
                    'total_connections' => $data['total_connections'] ?? 0,
                    'rejected_connections' => $data['rejected_connections'] ?? 0,
                    'curr_items' => $data['curr_items'] ?? 0, // Claves almacenadas
                    'bytes' => $data['bytes'] ?? 0, // Memoria usada
                    'limit_maxbytes' => $data['limit_maxbytes'] ?? 0, // Memoria máxima
                    'cmd_get' => $data['cmd_get'] ?? 0, // Comandos GET ejecutados
                    'cmd_set' => $data['cmd_set'] ?? 0, // Comandos SET ejecutados
                    'get_hits' => $data['get_hits'] ?? 0, // GET exitosos
                    'get_misses' => $data['get_misses'] ?? 0, // GET fallidos
                    'evictions' => $data['evictions'] ?? 0, // Claves expulsadas
                    'bytes_read' => $data['bytes_read'] ?? 0, // Bytes leídos
                    'bytes_written' => $data['bytes_written'] ?? 0, // Bytes escritos
                    'total_items' => $data['total_items'] ?? 0,
                ];
            }

            return $this->response('success', 'Se a recargado las estadísticas de Memcached.', ['info' => $memcachedStats]);
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al conectar con el servidor Memcached: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas para caché en base de datos.
     *
     * @return array Estadísticas de la caché en base de datos incluyendo cantidad de registros y uso de memoria
     */
    private function _getDatabaseStats(): array
    {
        try {
            $recordCount = DB::table('cache')->count();
            $tableInfo = DB::select("SHOW TABLE STATUS WHERE Name = 'cache'");

            $memory_usage = isset($tableInfo[0]) ? $this->formatBytes($tableInfo[0]->Data_length + $tableInfo[0]->Index_length) : 'N/A';

            return $this->response('success', 'Se ha recargado la información de la caché de base de datos.', ['item_count' => $recordCount, 'memory_usage' => $memory_usage]);
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas de la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas para caché en archivos.
     *
     * @return array Estadísticas de la caché en archivos incluyendo cantidad de archivos y uso de memoria
     */
    private function _getFilecacheStats(): array
    {
        try {
            $cachePath = config('cache.stores.file.path');
            $files = glob($cachePath . '/*');

            $memory_usage = $this->formatBytes(array_sum(array_map('filesize', $files)));

            return $this->response('success', 'Se ha recargado la información de la caché de archivos.', ['item_count' => count($files), 'memory_usage' => $memory_usage]);
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas de archivos: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas específicas de Redis para la caché.
     *
     * @return array Estadísticas de Redis incluyendo cantidad de claves y uso de memoria
     */
    private function _getRedisStats()
    {
        try {
            $prefix = config('cache.prefix'); // Asegúrate de agregar el sufijo correcto si es necesario

            $info = Redis::info();
            $keys = Redis::connection('cache')->keys($prefix . '*');

            $memory_usage = $this->formatBytes($info['used_memory'] ?? 0);

            return $this->response('success', 'Se ha recargado la información de la caché de Redis.', ['item_count' => count($keys), 'memory_usage' => $memory_usage]);
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas de Redis: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas específicas de Memcached para la caché.
     *
     * @return array Estadísticas de Memcached incluyendo cantidad de elementos y uso de memoria
     */
    public function _getMemcachedStats(): array
    {
        try {
            // Obtener estadísticas generales del servidor
            $stats = Cache::getStore()->getMemcached()->getStats();

            if (empty($stats)) {
                return $this->response('error', 'No se pudieron obtener las estadísticas del servidor Memcached.', ['item_count' => 0, 'memory_usage' => 0]);
            }

            // Usar el primer servidor configurado (en la mayoría de los casos hay uno)
            $serverStats = array_shift($stats);

            return $this->response(
                'success',
                'Estadísticas del servidor Memcached obtenidas correctamente.',
                [
                    'item_count' => $serverStats['curr_items'] ?? 0, // Número total de claves
                    'memory_usage' => $this->formatBytes($serverStats['bytes'] ?? 0), // Memoria usada
                    'max_memory' => $this->formatBytes($serverStats['limit_maxbytes'] ?? 0), // Memoria máxima asignada
                ]
            );
        } catch (\Exception $e) {
            return $this->response('danger', 'Error al obtener estadísticas de Memcached: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene información sobre las bases de datos Redis en uso.
     *
     * Analiza y recopila información sobre las diferentes bases de datos Redis
     * configuradas en el sistema (default, cache, sessions).
     *
     * @return array Información detallada de las bases de datos Redis
     */
    private function getRedisDatabases(): array
    {
        // Verificar si Redis está en uso
        $isRedisUsed = collect([
            config('cache.default'),
            config('session.driver'),
            config('queue.default'),
        ])->contains('redis');

        if (!$isRedisUsed) {
            return []; // Si Redis no está en uso, devolver un arreglo vacío
        }

        // Configuraciones de bases de datos de Redis según su uso
        $databases = [
            'default' => config('database.redis.default.database', 0), // REDIS_DB
            'cache' => config('database.redis.cache.database', 0), // REDIS_CACHE_DB
            'sessions' => config('database.redis.sessions.database', 0), // REDIS_SESSION_DB
        ];

        $result = [];
        $totalKeys = 0;

        // Recorrer solo las bases configuradas y activas
        foreach ($databases as $type => $db) {
            Redis::select($db); // Seleccionar la base de datos

            $keys = Redis::dbsize(); // Contar las claves en la base

            if ($keys > 0) {
                $result[$type] = [
                    'database' => $db,
                    'keys' => $keys,
                ];

                $totalKeys += $keys;
            }
        }

        if (!empty($result)) {
            $result['total_keys'] = $totalKeys;
        }

        return $result;
    }

    /**
     * Limpia la caché almacenada en base de datos.
     *
     * @return bool True si se eliminaron registros, False si no había registros para eliminar
     */
    private function clearDatabaseCache(): bool
    {
        $count = DB::table(config('cache.stores.database.table'))->count();

        if ($count > 0) {
            DB::table(config('cache.stores.database.table'))->truncate();
            return true;
        }

        return false;
    }

    /**
     * Limpia la caché almacenada en archivos.
     *
     * @return bool True si se eliminaron archivos, False si no había archivos para eliminar
     */
    private function clearFilecache(): bool
    {
        $cachePath = config('cache.stores.file.path');
        $files = glob($cachePath . '/*');

        if (!empty($files)) {
            File::deleteDirectory($cachePath);
            return true;
        }

        return false;
    }

    /**
     * Limpia la caché almacenada en Redis.
     *
     * @return bool True si se eliminaron claves, False si no había claves para eliminar
     */
    private function clearRedisCache(): bool
    {
        $prefix = config('cache.prefix', '');
        $keys = Redis::connection('cache')->keys($prefix . '*');

        if (!empty($keys)) {
            Redis::connection('cache')->flushdb();

            // Simulate cache clearing delay
            sleep(1);

            return true;
        }

        return false;
    }

    /**
     * Limpia la caché almacenada en Memcached.
     *
     * @return bool True si se limpió la caché, False en caso contrario
     */
    private function clearMemcachedCache(): bool
    {
        // Obtener el cliente Memcached directamente
        $memcached = Cache::store('memcached')->getStore()->getMemcached();

        // Ejecutar flush para eliminar todo
        if ($memcached->flush()) {
            // Simulate cache clearing delay
            sleep(1);

            return true;
        }

        return false;
    }

    /**
     * Verifica si un driver es soportado por el sistema.
     *
     * @param string $driver Nombre del driver a verificar
     * @return bool True si el driver es soportado, False en caso contrario
     */
    private function isSupportedDriver(string $driver): bool
    {
        return in_array($driver, ['redis', 'memcached', 'database', 'file']);
    }

    /**
     * Convierte bytes en un formato legible por humanos.
     *
     * @param int|float $bytes Cantidad de bytes a formatear
     * @return string Cantidad formateada con unidad (B, KB, MB, GB, TB)
     */
    private function formatBytes(int|float $bytes): string
    {
        if ($bytes < 1) {
            return '0 B';
        }

        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor(log($bytes, 1024)); // Más preciso y directo
        $formatted = $bytes / pow(1024, $factor);

        return sprintf('%.2f', $formatted) . ' ' . $sizes[$factor];
    }

    /**
     * Genera una respuesta estandarizada para las operaciones del servicio.
     *
     * @param string $status Estado de la operación ('success', 'warning', 'danger', 'info')
     * @param string $message Mensaje descriptivo de la operación
     * @param array $data Datos adicionales de la operación
     * @return array Respuesta estructurada con estado, mensaje y datos
     */
    private function response(string $status, string $message, array $data = []): array
    {
        return array_merge(compact('status', 'message'), $data);
    }
}
