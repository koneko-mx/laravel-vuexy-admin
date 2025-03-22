<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Servicio para gestionar y obtener información de configuración del sistema de caché.
 *
 * Esta clase proporciona métodos para obtener información detallada sobre las configuraciones
 * de caché, sesión, base de datos y drivers del sistema. Permite consultar versiones,
 * estados y configuraciones de diferentes servicios como Redis, Memcached y bases de datos.
 */
class CacheConfigService
{
    /**
     * Obtiene la configuración completa del sistema de caché y servicios relacionados.
     *
     * @return array Configuración completa que incluye caché, sesión, base de datos y drivers
     */
    public function getConfig(): array
    {
        return [
            'cache' => $this->getCacheConfig(),
            'session' => $this->getSessionConfig(),
            'database' => $this->getDatabaseConfig(),
            'driver' => $this->getDriverVersion(),
            'memcachedInUse' => $this->isDriverInUse('memcached'),
            'redisInUse' => $this->isDriverInUse('redis'),
        ];
    }

    /**
     * Obtiene la configuración específica del sistema de caché.
     *
     * @return array Configuración del caché incluyendo driver, host y base de datos
     */
    private function getCacheConfig(): array
    {
        $cacheConfig = Config::get('cache');
        $driver = $cacheConfig['default'];

        switch ($driver) {
            case 'redis':
                $connection = config('database.redis.cache');
                $cacheConfig['host'] = $connection['host'] ?? 'localhost';
                $cacheConfig['database'] = $connection['database'] ?? 'N/A';
                break;

            case 'database':
                $connection = config('database.connections.' . config('cache.stores.database.connection'));
                $cacheConfig['host'] = $connection['host'] ?? 'localhost';
                $cacheConfig['database'] = $connection['database'] ?? 'N/A';
                break;

            case 'memcached':
                $servers = config('cache.stores.memcached.servers');
                $cacheConfig['host'] = $servers[0]['host'] ?? 'localhost';
                $cacheConfig['database'] = 'N/A';
                break;

            case 'file':
                $cacheConfig['host'] = storage_path('framework/cache/data');
                $cacheConfig['database'] = 'N/A';
                break;

            default:
                $cacheConfig['host'] = 'N/A';
                $cacheConfig['database'] = 'N/A';
                break;
        }

        return $cacheConfig;
    }

    /**
     * Obtiene la configuración del sistema de sesiones.
     *
     * @return array Configuración de sesiones incluyendo driver, host y base de datos
     */
    private function getSessionConfig(): array
    {
        $sessionConfig = Config::get('session');
        $driver = $sessionConfig['driver'];

        switch ($driver) {
            case 'redis':
                $connection = config('database.redis.sessions');
                $sessionConfig['host'] = $connection['host'] ?? 'localhost';
                $sessionConfig['database'] = $connection['database'] ?? 'N/A';
                break;

            case 'database':
                $connection = config('database.connections.' . $sessionConfig['connection']);
                $sessionConfig['host'] = $connection['host'] ?? 'localhost';
                $sessionConfig['database'] = $connection['database'] ?? 'N/A';
                break;

            case 'memcached':
                $servers = config('cache.stores.memcached.servers');
                $sessionConfig['host'] = $servers[0]['host'] ?? 'localhost';
                $sessionConfig['database'] = 'N/A';
                break;

            case 'file':
                $sessionConfig['host'] = storage_path('framework/sessions');
                $sessionConfig['database'] = 'N/A';
                break;

            default:
                $sessionConfig['host'] = 'N/A';
                $sessionConfig['database'] = 'N/A';
                break;
        }

        return $sessionConfig;
    }

    /**
     * Obtiene la configuración de la base de datos principal.
     *
     * @return array Configuración de la base de datos incluyendo host y nombre de la base de datos
     */
    private function getDatabaseConfig(): array
    {
        $databaseConfig = Config::get('database');
        $connection = $databaseConfig['default'];

        $connectionConfig = config('database.connections.' . $connection);
        $databaseConfig['host'] = $connectionConfig['host'] ?? 'localhost';
        $databaseConfig['database'] = $connectionConfig['database'] ?? 'N/A';

        return $databaseConfig;
    }

    /**
     * Obtiene información sobre las versiones de los drivers en uso.
     *
     * Recopila información detallada sobre las versiones de los drivers de base de datos,
     * Redis y Memcached si están en uso en el sistema.
     *
     * @return array Información de versiones de los drivers activos
     */
    private function getDriverVersion(): array
    {
        $drivers = [];
        $defaultDatabaseDriver = config('database.default'); // Obtén el driver predeterminado

        switch ($defaultDatabaseDriver) {
            case 'mysql':
            case 'mariadb':
                $drivers['mysql'] = [
                    'version' => $this->getMySqlVersion(),
                    'details' => config("database.connections.$defaultDatabaseDriver"),
                ];

                $drivers['mariadb'] = $drivers['mysql'];

            case 'pgsql':
                $drivers['pgsql'] = [
                    'version' => $this->getPgSqlVersion(),
                    'details' => config("database.connections.pgsql"),
                ];
                break;

            case 'sqlsrv':
                $drivers['sqlsrv'] = [
                    'version' => $this->getSqlSrvVersion(),
                    'details' => config("database.connections.sqlsrv"),
                ];
                break;

            default:
                $drivers['unknown'] = [
                    'version' => 'No disponible',
                    'details' => 'Driver no identificado',
                ];
                break;
        }

        // Opcional: Agrega detalles de Redis y Memcached si están en uso
        if ($this->isDriverInUse('redis')) {
            $drivers['redis'] = [
                'version' => $this->getRedisVersion(),
            ];
        }

        if ($this->isDriverInUse('memcached')) {
            $drivers['memcached'] = [
                'version' => $this->getMemcachedVersion(),
            ];
        }

        return $drivers;
    }

    /**
     * Obtiene la versión del servidor MySQL.
     *
     * @return string Versión del servidor MySQL o mensaje de error
     */
    private function getMySqlVersion(): string
    {
        try {
            $version = DB::selectOne('SELECT VERSION() as version');
            return $version->version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Obtiene la versión del servidor PostgreSQL.
     *
     * @return string Versión del servidor PostgreSQL o mensaje de error
     */
    private function getPgSqlVersion(): string
    {
        try {
            $version = DB::selectOne("SHOW server_version");
            return $version->server_version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Obtiene la versión del servidor SQL Server.
     *
     * @return string Versión del servidor SQL Server o mensaje de error
     */
    private function getSqlSrvVersion(): string
    {
        try {
            $version = DB::selectOne("SELECT @@VERSION as version");
            return $version->version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Obtiene la versión del servidor Memcached.
     *
     * @return string Versión del servidor Memcached o mensaje de error
     */
    private function getMemcachedVersion(): string
    {
        try {
            $memcached = new \Memcached();
            $memcached->addServer(
                Config::get('cache.stores.memcached.servers.0.host'),
                Config::get('cache.stores.memcached.servers.0.port')
            );

            $stats = $memcached->getStats();
            foreach ($stats as $serverStats) {
                return $serverStats['version'] ?? 'No disponible';
            }

            return 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Obtiene la versión del servidor Redis.
     *
     * @return string Versión del servidor Redis o mensaje de error
     */
    private function getRedisVersion(): string
    {
        try {
            $info = Redis::info();
            return $info['redis_version'] ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Verifica si un driver específico está en uso en el sistema.
     *
     * Comprueba si el driver está siendo utilizado en caché, sesiones o colas.
     *
     * @param string $driver Nombre del driver a verificar
     * @return bool True si el driver está en uso, false en caso contrario
     */
    protected function isDriverInUse(string $driver): bool
    {
        return in_array($driver, [
            Config::get('cache.default'),
            Config::get('session.driver'),
            Config::get('queue.default'),
        ]);
    }
}
