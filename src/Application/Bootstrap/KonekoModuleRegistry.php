<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap;

use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModule;

class KonekoModuleRegistry
{
    /** @var KonekoModule[] */
    protected static array $modules = [];

    /**
     * Registra un módulo completo.
     */
    public static function register(KonekoModule $module): void
    {
        static::$modules[$module->componentNamespace] = $module;
    }

    /**
     * Verifica si un módulo está registrado.
     */
    public static function has(string $name): bool
    {
        return isset(static::$modules[$name]);
    }

    /**
     * Elimina un módulo del registro.
     */
    public static function unregister(string $name): void
    {
        unset(static::$modules[$name]);
    }

    /**
     * Devuelve todos los módulos registrados.
     */
    public static function all(): array
    {
        return static::$modules;
    }

    /**
     * Devuelve todos los módulos como Collection.
     */
    public static function asCollection(): Collection
    {
        return collect(static::$modules);
    }

    /**
     * Devuelve un módulo por nombre.
     */
    public static function get(string $name): ?KonekoModule
    {
        return static::$modules[$name] ?? null;
    }

    /**
     * Filtra módulos por tag.
     */
    public static function withTag(string $tag): array
    {
        return static::asCollection()
            ->filter(fn(KonekoModule $m) => $m->hasTag($tag))
            ->all();
    }

    /**
     * Módulos con la bandera `enabled = true` (en el futuro para UI).
     */
    public static function enabled(): array
    {
        return static::asCollection()
            ->filter(fn(KonekoModule $m) => $m->enabled ?? true)
            ->all();
    }

    /**
     * Módulos con la bandera `enabled = false`.
     */
    public static function disabled(): array
    {
        return static::asCollection()
            ->filter(fn(KonekoModule $m) => !($m->enabled ?? true))
            ->all();
    }

    public static function discoverModule(string $basePath, int $maxDepth = 3): ?KonekoModule
    {
        $targetFiles = ['src/vuexy-admin.module.php', 'src/koneko-vuexy.module.php'];
        $level = 0;
        $dirs = [$basePath];

        while ($level++ <= $maxDepth && !empty($dirs)) {
            $nextDirs = [];

            foreach ($dirs as $dir) {
                foreach ($targetFiles as $relativePath) {
                    $fullPath = rtrim($dir, '/') . '/' . $relativePath;

                    if (file_exists($fullPath)) {
                        $module = KonekoModule::fromModuleFile($fullPath);

                        if ($module instanceof KonekoModule) {
                            KonekoModuleRegistry::register($module);
                            return $module;
                        }
                    }
                }

                // Agregamos subdirectorios para el próximo nivel
                foreach (scandir($dir) as $entry) {
                    if ($entry === '.' || $entry === '..') continue;

                    $subPath = $dir . '/' . $entry;
                    if (is_dir($subPath)) {
                        $nextDirs[] = $subPath;
                    }
                }
            }

            $dirs = $nextDirs;
        }

        return null;
    }

    /**
     * Mapa resumido para tarjetas en UI.
     */
    public static function getSummaries(): array
    {
        return static::asCollection()
            ->map(fn(KonekoModule $m) => $m->toSummary())
            ->values()
            ->all();
    }

    /**
     * Detalle completo de módulos (modo debug o exportación).
     */
    public static function debugDump(): array
    {
        return static::asCollection()
            ->map(fn(KonekoModule $m) => $m->getDetails())
            ->all();
    }
}
