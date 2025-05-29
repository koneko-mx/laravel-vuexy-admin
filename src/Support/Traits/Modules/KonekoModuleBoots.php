<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Modules;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModule;
use Koneko\VuexyAdmin\Application\Bootstrap\Manager\KonekoModuleBootManager;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;

/**
 * Trait universal para bootstrapping de módulos Vuexy,
 * incluyendo registro, publicaciones y futuras extensiones.
 */
trait KonekoModuleBoots
{
    protected function registerKonekoModule(string $modulePath): void
    {
        $module = KonekoModule::fromModuleDirectory($modulePath);

        KonekoModuleRegistry::register($module); // ✅ lo registramos en el registro

        foreach ($module->publishedFiles as $tag => $files) {
            $resolvedFiles = [];

            foreach ($files as $from => $to) {
                $fromFull = KonekoModuleBootManager::resolvePath($module, $from);

                if (file_exists($fromFull)) {
                    $resolvedFiles[$fromFull] = $to;

                } else {
                    Log::warning("📁 Archivo a publicar no encontrado: {$fromFull}");
                }
            }

            if (!empty($resolvedFiles) && $this instanceof ServiceProvider) {
                $this->publishes($resolvedFiles, "{$module->slug}-{$tag}");
            }
        }

        foreach ($module->aliases ?? [] as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }

        foreach ($module->bindings ?? [] as $abstract => $concrete) {
            app()->singleton($abstract, $concrete);
        }

        foreach ($module->singletons ?? [] as $singleton) {
            app()->singleton($singleton);
        }

        foreach ($module->macros ?? [] as $macroPath) {
            if (file_exists($macroPath)) {
                require_once $macroPath;
            }
        }

        // Si usas mergeConfigFrom en alguno, hazlo aquí también
        foreach ($module->configs ?? [] as $namespace => $relativePath) {
            $fullPath = $module->basePath . DIRECTORY_SEPARATOR . $relativePath;
            if (file_exists($fullPath)) {
                $this->mergeConfigFrom($fullPath, $namespace);
            }
        }
    }

    protected function registerAllKonekoModules(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            // Alias, singletons, bindings
            foreach ($module->aliases ?? [] as $alias => $class) {
                AliasLoader::getInstance()->alias($alias, $class);
            }

            foreach ($module->bindings ?? [] as $abstract => $concrete) {
                app()->singleton($abstract, $concrete);
            }

            foreach ($module->singletons ?? [] as $singleton) {
                app()->singleton($singleton);
            }
        }
    }

    protected function bootKonekoModule(string $modulePath): void
    {
        KonekoModuleBootManager::boot(KonekoModule::fromModuleFile($modulePath));
    }
}
