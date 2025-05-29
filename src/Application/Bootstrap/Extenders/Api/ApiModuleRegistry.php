<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Api;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModule;
use Koneko\VuexyApisAndIntegrations\Models\ExternalApi;
use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoComponentContextRegistrar;

class ApiModuleRegistry
{
    /**
     * Importa APIs desde el archivo api.json de cada módulo.
     */
    public static function importModuleApis(KonekoModule $module): void
    {
        $path = base_path($module->basePath . '/database/data/apis/api.json');

        if (!File::exists($path)) {
            Log::info("[API Import] No se encontró archivo api.json para el módulo {$module->slug}");
            return;
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            Log::warning("[API Import] Formato inválido en {$path}");
            return;
        }

        foreach ($data as $entry) {
            if (empty($entry['slug']) || empty($entry['provider']) || empty($entry['base_url'])) {
                Log::warning("[API Import] Entrada inválida: se omite", $entry);
                continue;
            }

            $entry['module'] = $module->componentNamespace ?? $module->slug;

            ExternalApi::updateOrCreate(
                ['slug' => $entry['slug'], 'module' => $entry['module']],
                collect($entry)->except(['slug', 'module'])->toArray()
            );
        }
    }

    /**
     * Exporta todas las APIs de un módulo al archivo api.json.
     */
    public static function exportModuleApis(KonekoModule $module): void
    {
        $apis = ExternalApi::where('module', $module->componentNamespace ?? $module->slug)->get();

        $path = base_path($module->basePath . '/database/data/apis/api.json');
        File::ensureDirectoryExists(dirname($path));

        File::put($path, json_encode($apis->map(function ($api) {
            return collect($api->toArray())->except(['id', 'created_by', 'updated_by', 'created_at', 'updated_at'])->toArray();
        }), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Exporta todas las APIs del sistema por módulo.
     */
    public static function exportAll(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            self::exportModuleApis($module);
        }
    }

    /**
     * Importa APIs de todos los módulos registrados.
     */
    public static function importAll(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            self::importModuleApis($module);
        }
    }

    /**
     * Devuelve todas las APIs del módulo actual.
     */
    public static function forCurrentModule(): Collection
    {
        return ExternalApi::where('module', KonekoComponentContextRegistrar::currentComponent() ?? 'unknown')->get();
    }
}
