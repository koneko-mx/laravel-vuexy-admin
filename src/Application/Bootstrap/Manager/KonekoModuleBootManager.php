<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap\Manager;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\{App, Blade, Event, Lang, Route, View};
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;
use Koneko\VuexyAdmin\Application\Factories\FactoryExtensionRegistry;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model\ModelExtensionRegistry;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModule;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\Config\Registry\ConfigBlockRegistry;
use Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry;
use Livewire\Livewire;

class KonekoModuleBootManager
{
    public static function bootAll(): void
    {
        $modulesActivated = [];

        foreach (KonekoModuleRegistry::enabled() as $module) {
            static::boot($module);
            $modulesActivated[] = $module->slug;
        }
    }

    public static function boot(KonekoModule $module): void
    {
        // =========================================
        // 1️⃣ CONFIGURACIONES DEL MÓDULO
        // =========================================
        foreach ($module->configs ?? [] as $namespace => $relativePath) {
            $filename    = basename($relativePath);
            $projectPath = config_path($filename);
            $modulePath  = $module->basePath . DIRECTORY_SEPARATOR . $relativePath;

            $moduleCfg  = file_exists($modulePath)  ? (array) require $modulePath  : [];
            $projectCfg = file_exists($projectPath) ? (array) require $projectPath : [];

            [$namespace, $mode] = array_pad(explode('@', $namespace, 2), 2, 'smart');

            $merged = match ($mode) {
                'append'  => array_values(array_unique(array_merge((array) config($namespace, []), $moduleCfg, $projectCfg), SORT_REGULAR)),
                'replace' => $projectCfg ?: $moduleCfg,
                default   => self::smartMerge((array) config($namespace, []), self::smartMerge($moduleCfg, $projectCfg)),
            };

            config()->set($namespace, $merged);
        }

        // =========================================
        // 2️⃣ INFRAESTRUCTURA BASE (App Bindings)
        // =========================================
        foreach ($module->middleware ?? [] as $alias => $middlewareClass) {
            app(Router::class)->aliasMiddleware($alias, $middlewareClass);
        }

        foreach ($module->providers ?? [] as $provider) {
            App::register($provider);
        }

        foreach ($module->aliases ?? [] as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }

        foreach ($module->Singletons ?? [] as $singleton) {
            app()->singleton($singleton);
        }

        foreach ($module->bindings ?? [] as $abstract => $concrete) {
            app()->singleton($abstract, $concrete);
        }

        // =========================================
        // 3️⃣ FUNCIONALIDADES COMPLEMENTARIAS
        // =========================================
        foreach ($module->macros ?? [] as $macroPath) {
            $fullPath = static::resolvePath($module, $macroPath);
            if (file_exists($fullPath)) {
                require_once $fullPath;

            } else {
                logger()->warning("[KonekoModuleBootManager] ⚠️ Archivo de macro no encontrado: $fullPath");
            }
        }

        foreach ($module->listeners ?? [] as $event => $listener) {
            Event::listen($event, $listener);
        }

        foreach ($module->observers ?? [] as $model => $observers) {
            if (!class_exists($model)) {
                logger()->warning("🔍 Modelo no encontrado para observers: $model");
                continue;
            }

            foreach ((array) $observers as $observer) {
                if (class_exists($observer)) {
                    $model::observe($observer);

                } else {
                    logger()->warning("⚠️ Observer no válido: {$observer}");
                }
            }
        }

        foreach ($module->auditable ?? [] as $model) {
            if (class_exists($model)) {
                $model::observe(\OwenIt\Auditing\AuditableObserver::class);
            }
        }

        // =========================================
        // 4️⃣ DEFINICIONES TÉCNICAS Y RUTAS
        // =========================================
        foreach ($module->migrations ?? [] as $relativePath) {
            $fullPath = static::resolvePath($module, $relativePath);
            if (is_dir($fullPath)) {
                app()->make('migrator')->path($fullPath);
            }
        }

        foreach ($module->routes ?? [] as $routeGroup) {
            $middleware = $routeGroup['middleware'] ?? [];
            $paths = $routeGroup['paths'] ?? [];

            foreach ($paths as $relativePath) {
                $fullPath = static::resolvePath($module, $relativePath);

                if (file_exists($fullPath)) {
                    Route::middleware($middleware)->group(function () use ($fullPath) {
                        require $fullPath;
                    });
                }
            }
        }

        // =========================================
        // 5️⃣ RECURSOS: VISTAS, TRADUCCIONES Y COMPONENTES
        // =========================================
        foreach ($module->views ?? [] as $namespace => $path) {
            View::addNamespace($namespace, static::resolvePath($module, $path));
        }

        foreach ($module->translations ?? [] as $namespace => $path) {
            Lang::addNamespace($namespace, static::resolvePath($module, $path));
        }

        foreach ($module->bladeComponents ?? [] as $prefix => $namespace) {
            Blade::componentNamespace($namespace, $prefix);
        }

        foreach ($module->livewire ?? [] as $namespace => $components) {
            foreach ($components as $alias => $class) {
                Livewire::component("{$namespace}::{$alias}", $class);
            }
        }

        // =========================================
        // 6️⃣ CONSOLA Y DEFINICIONES EXTERNAS
        // =========================================
        if (App::runningInConsole() && !empty($module->commands)) {
            static::registerCommands($module->commands);
        }

        if (!empty($module->apis['catalog_path'])) {
            $apiCatalogPath = static::resolvePath($module, $module->apis['catalog_path']);

            if (file_exists($apiCatalogPath)) {
                $apiDefinitions = require $apiCatalogPath;
                config()->set("apis.{$module->slug}", $apiDefinitions);
            }
        }

        // =========================================
        // 7️⃣ REGISTROS INTERNOS
        // =========================================
        foreach ($module->catalogs ?? [] as $catalog => $service) {
            CatalogModuleRegistry::register($catalog, $service);
        }

        foreach ($module->extensions['config'] ?? [] as $builder => $extensions) {
            ModelExtensionRegistry::registerConfigExtensions($builder, $extensions);
        }

        foreach ($module->extensions['model_flags'] ?? [] as $model => $flags) {
            ModelExtensionRegistry::registerModelFlags($model, $flags);
        }

        foreach ($module->extensions['factory_traits'] ?? [] as $factory => $traits) {
            foreach ((array) $traits as $trait) {
                FactoryExtensionRegistry::registerFactoryTrait($factory, $trait);
            }
        }

        foreach ($module->scopeModels ?? [] as $scope => $modelClass) {
            try {
                ScopeRegistry::register($scope, $modelClass);

            } catch (\Throwable $e) {
                logger()->error("[ScopeRegistry] No se pudo registrar el scope '{$scope}' del módulo '{$module->slug}': {$e->getMessage()}");
            }
        }

        foreach ($module->configBlocks ?? [] as $configKey => $blockDefinition) {
            try {
                ConfigBlockRegistry::register($configKey, $blockDefinition);

            } catch (\Throwable $e) {
                logger()->warning("[ConfigBlockRegistry] No se pudo registrar el bloque '$configKey': {$e->getMessage()}");
            }
        }
    }

    /**
     * Resuelve una ruta relativa al root del módulo.
     */
    public static function resolvePath(KonekoModule $module, ?string $relativePath): string
    {
        return $module->basePath . '/' . $relativePath;
    }

    /**
     * Merge “inteligente”:
     * - arrays asociativos: override recursivo (derecha gana)
     * - arrays indexados: append + unique (SORT_REGULAR)
     */
    private static function smartMerge(array $left, array $right): array
    {
        // ¿Alguno no es array “lista”? tratamos por clave (assoc)
        if (!array_is_list($left) || !array_is_list($right)) {
            // merge recursivo por claves (derecha gana)
            $out = $left;
            foreach ($right as $k => $v) {
                $out[$k] = (isset($out[$k]) && is_array($out[$k]) && is_array($v))
                    ? self::smartMerge($out[$k], $v)
                    : $v;
            }
            return $out;
        }

        // Ambos son listas → “push” (append) + sin duplicados
        return array_values(array_unique(array_merge($left, $right), SORT_REGULAR));
    }

    /*
     * Registra comandos
     */
    public static function registerCommands(array $commands): void
    {
        foreach ($commands as $command) {
            if (class_exists($command)) {
                \Illuminate\Console\Application::starting(function ($artisan) use ($command) {
                    $artisan->add(App::make($command));
                });
            }
        }
    }

    public static function scheduleAll(Schedule $schedule): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            static::schedule($schedule, $module);
        }
    }

    public static function schedule(Schedule $schedule, KonekoModule $module): void
    {
        foreach ($module->schedules ?? [] as $entry) {
            $jobClass = $entry['job'] ?? null;
            $method   = $entry['method'] ?? null;
            $params   = $entry['params'] ?? [];
            $chain    = $entry['chain'] ?? [];

            if (!class_exists($jobClass)) {
                logger()->warning("[VuexySchedule] ❌ Job no encontrado: {$jobClass}");
                continue;
            }

            // Crea el evento del Job
            $event = $schedule->job(new $jobClass());

            // Intenta aplicar el método al evento, no al schedule
            if ($method && method_exists($event, $method)) {
                $event = $event->{$method}(...$params);

            } else {
                logger()->warning("[VuexySchedule] ❌ Método inválido para evento: {$method}");
                continue;
            }

            // Encadenamientos adicionales
            foreach ($chain as $chainMethod) {
                if (method_exists($event, $chainMethod)) {
                    $event = $event->{$chainMethod}();

                } else {
                    logger()->warning("[VuexySchedule] ⚠️ Método de chain no válido: {$chainMethod} en {$jobClass}");
                }
            }

            logger()->debug("[VuexySchedule] ⏱️ Job programado correctamente: {$jobClass} usando {$method}()");
        }
    }
}
