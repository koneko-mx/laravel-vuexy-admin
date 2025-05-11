<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\{App,Blade,Event,Lang,Route,View};
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Catalog\CatalogModuleRegistry;
use Koneko\VuexyAdmin\Application\Factories\FactoryExtensionRegistry;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model\ModelExtensionRegistry;
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
        // ⚙️ Archivos de configuración del módulo
        foreach ($module->configs ?? [] as $namespace => $relativePath) {
            $fullPath = $module->basePath . DIRECTORY_SEPARATOR . $relativePath;

            if (file_exists($fullPath)) {
                $config = require $fullPath;

                if (is_array($config)) {
                    config()->set($namespace, array_merge(
                        config($namespace, []),
                        $config
                    ));
                }
            }
        }

        // 🛡️ Middleware
        foreach ($module->middleware ?? [] as $alias => $middlewareClass) {
            // @var Router $router
            $router = app(Router::class);
            $router->aliasMiddleware($alias, $middlewareClass);
        }

        // 🏭 Proveedores de servicio
        foreach ($module->providers ?? [] as $provider) {
            App::register($provider);
        }

        // 🧩 Alias de clases
        foreach ($module->aliases ?? [] as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }

        // 🔩 Singletons
        foreach ($model->Singletons?? [] as $singletone) {
            app()->singleton($singletone);
        }

        // 🔗 Bindings de interfaces a servicios
        foreach ($module->bindings ?? [] as $abstract => $concrete) {
            app()->singleton($abstract, $concrete);
        }

        // ⚙️ Namespace de componentes
        if (!empty($module->componentNamespace)) {
            KonekoComponentContextRegistrar::registerComponent($module->componentNamespace);
        }

        // 📜 Macros
        foreach ($module->macros ?? [] as $macroPath) {
            $fullPath = static::resolvePath($module, $macroPath);

            if (file_exists($fullPath)) {
                require_once $fullPath;

            } else {
                logger()->warning("[KonekoModuleBootManager] ⚠️ Archivo de macro no encontrado: $fullPath");
            }
        }

        // 🔊 Eventos
        foreach ($module->listeners ?? [] as $event => $listener) {
            Event::listen($event, $listener);
        }

        // 🔍 Observadores
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


        // 🧪 Modelos auditables
        foreach ($module->auditable ?? [] as $model) {
            if (class_exists($model)) {
                $model::observe(\OwenIt\Auditing\AuditableObserver::class);
            }
        }

        // 🧬 Migraciones
        foreach ($module->migrations ?? [] as $relativePath) {
            $fullPath = static::resolvePath($module, $relativePath);

            if (is_dir($fullPath)) {
                app()->make('migrator')->path($fullPath);
            }
        }

        // 🗺️ Rutas (después de setModule)
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

        // 🗂️ Vistas
        foreach ($module->views ?? [] as $namespace => $path) {
            View::addNamespace($namespace, static::resolvePath($module, $path));
        }

        // 🌍 Traducciones
        foreach ($module->translations ?? [] as $namespace => $path) {
            Lang::addNamespace($namespace, static::resolvePath($module, $path));
        }

        // 🧩 Componentes Blade
        foreach ($module->bladeComponents ?? [] as $prefix => $namespace) {
            Blade::componentNamespace($namespace, $prefix);
        }

        // ⚡ Livewire
        foreach ($module->livewire ?? [] as $namespace => $components) {
            foreach ($components as $alias => $class) {
                Livewire::component("{$namespace}::{$alias}", $class);
            }
        }

        // 🛠 Comandos Artisan
        if (App::runningInConsole() && !empty($module->commands)) {
            static::registerCommands($module->commands);
        }

        // 🔗 Registro de APIs disponibles en el módulo
        if (!empty($module->apis['catalog_path'])) {
            $apiCatalogPath = static::resolvePath($module, $module->apis['catalog_path']);

            if (file_exists($apiCatalogPath)) {
                $apiDefinitions = require $apiCatalogPath;

                config()->set("apis.{$module->slug}", $apiDefinitions);
            }
        }

        // 📑 Registro de catálogos
        foreach ($module->catalogs ?? [] as $catalog => $service) {
            CatalogModuleRegistry::register($catalog, $service);
        }

        // 🧠 Extensiones de Modelos
        foreach ($module->extensions['config'] ?? [] as $builder => $extensions) {
            ModelExtensionRegistry::registerConfigExtensions($builder, $extensions);
        }

        // 🧪 Extensiones de Flags de Modelos
        foreach ($module->extensions['model_flags'] ?? [] as $model => $flags) {
            ModelExtensionRegistry::registerModelFlags($model, $flags);
        }

        // 🧬 Extensiones de Traits de Factory
        foreach ($module->extensions['factory_traits'] ?? [] as $factory => $traits) {
            foreach ((array) $traits as $trait) {
                FactoryExtensionRegistry::registerFactoryTrait($factory, $trait);
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
