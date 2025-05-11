<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Modules;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Koneko\VuexyAdmin\Application\Bootstrap\{KonekoModule,KonekoModuleRegistry};

/**
 * Trait universal para bootstrapping de módulos Vuexy,
 * incluyendo registro, publicaciones y futuras extensiones.
 */
trait __KonekoModuleBoots
{
    protected function bootKonekoModule(string $modulePath): void
    {
        $module = KonekoModule::fromModuleFile($modulePath);

        // ✅ Registrar en el sistema
        KonekoModuleRegistry::register($module);

        // 📁 Publicar archivos si existen
        if (!empty($module->publishedFiles)) {
            $this->registerPublishedFiles($module);
        }
    }

    /**
     * Registra los archivos publicables del módulo usando publishes()
     */
    protected function registerPublishedFiles(KonekoModule $module): void
    {
        foreach ($module->publishedFiles as $tag => $files) {
            $resolvedFiles = [];

            foreach ($files as $from => $to) {
                $fromFull = $this->resolvePath($module->basePath, $from);

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
    }

    protected function resolvePath(string $base, ?string $relative): string
    {
        return $base . '/' . ltrim($relative, '/');
    }
}
