<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CacheConfigService
{
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

    private function getDatabaseConfig(): array
    {
        $databaseConfig = Config::get('database');
        $connection = $databaseConfig['default'];

        $connectionConfig = config('database.connections.' . $connection);
        $databaseConfig['host'] = $connectionConfig['host'] ?? 'localhost';
        $databaseConfig['database'] = $connectionConfig['database'] ?? 'N/A';

        return $databaseConfig;
    }


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

    private function getMySqlVersion(): string
    {
        try {
            $version = DB::selectOne('SELECT VERSION() as version');
            return $version->version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function getPgSqlVersion(): string
    {
        try {
            $version = DB::selectOne("SHOW server_version");
            return $version->server_version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function getSqlSrvVersion(): string
    {
        try {
            $version = DB::selectOne("SELECT @@VERSION as version");
            return $version->version ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

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

    private function getRedisVersion(): string
    {
        try {
            $info = Redis::info();
            return $info['redis_version'] ?? 'No disponible';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }


    protected function isDriverInUse(string $driver): bool
    {
        return in_array($driver, [
            Config::get('cache.default'),
            Config::get('session.driver'),
            Config::get('queue.default'),
        ]);
    }
}
