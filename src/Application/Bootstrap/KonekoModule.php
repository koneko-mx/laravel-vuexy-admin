<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap;

use Illuminate\Support\Str;

class KonekoModule
{
    // === Identidad del Módulo ===
    public ?string $vendor;
    public string $name;
    public string $slug;
    public string $description;
    public string $type;
    public array $tags;
    public string $version;
    public array $keywords;
    public array $authors;
    public ?array $support;
    public ?string $license;
    public string $minimumStability;
    public string $buildVersion;

    // === Namespace de configuraciones ===
    public string $componentNamespace;

    // === Datos de composer/autoload ===
    public string $composerName;
    public string $namespace;
    public ?string $provider;
    public string $basePath;
    public string $composerPath;
    public array $dependencies;

    // === Metadatos visuales para UI del gestor ===
    public array $ui;

    // === Archivos de configuraciones ===
    public array $configs;

    // === Providers, Middleware y Aliases (runtime) ===
    public array $providers;
    public array $middleware;
    public array $aliases;

    // === Singleton *Nuevo Prototipo ===
    public array $singletons;

    // === Bindings de interfaces a servicios ===
    public array $bindings;

    // === Macro ===
    public array $macros;

    // === Observadores y Auditable ===
    public array $observers;
    public array $listeners;
    public array $auditable;

    // === Migraciones ===
    public array $migrations;

    // === Rutas ===
    public array $routes;

    // === Vistas y traducciones ===
    public array $views;
    public array $translations;

    // === Livewire, Blade, Vistas ===
    public array $bladeComponents;
    public array $livewire;

    // === Archivos publicables ===
    public array $publishedFiles;

    // === Comandos Artisan ===
    public array $commands;

    public array $schedules;

    // === Roles y permisos ===
    public array $rbac;

    // === APIs ===
    public array $apis;

    // === Catálogos ===
    public array $catalogs;

    // === Extensiones del ecosistema ===
    public array $extensions;

    public function __construct(array $overrides = [])
    {
        $this->vendor          = $overrides['vendor']          ?? null;
        $this->name            = $overrides['name']            ?? '';
        $this->slug            = $overrides['slug']            ?? '';
        $this->description     = $overrides['description']     ?? '';
        $this->type            = $overrides['type']            ?? 'plugin';
        $this->tags            = $overrides['tags']            ?? [];
        $this->version         = $overrides['version']         ?? '1.0.0';
        $this->keywords        = $overrides['keywords']        ?? [];
        $this->authors         = $overrides['authors']         ?? [];
        $this->support         = $overrides['support']         ?? [];
        $this->license         = $overrides['license']         ?? null;
        $this->minimumStability= $overrides['minimumStability']?? 'stable';
        $this->buildVersion    = $overrides['buildVersion']    ?? now()->format('YmdHis');

        $this->componentNamespace = $overrides['componentNamespace'] ?? '';

        $this->composerName    = $overrides['composerName']    ?? '';
        $this->namespace       = $overrides['namespace']       ?? '';
        $this->provider        = $overrides['provider']        ?? null;
        $this->basePath        = $overrides['basePath']        ?? '';
        $this->composerPath    = $overrides['composerPath']    ?? '';
        $this->dependencies    = $overrides['dependencies']    ?? [];

        $this->ui              = $overrides['ui']              ?? [];

        $this->configs         = $overrides['configs']         ?? [];

        $this->providers       = $overrides['providers']       ?? [];
        $this->middleware      = $overrides['middleware']      ?? [];
        $this->aliases         = $overrides['aliases']         ?? [];

        $this->singletons      = $overrides['singletons']      ?? [];
        $this->bindings        = $overrides['bindings']        ?? [];
        $this->macros          = $overrides['macros']          ?? [];

        $this->observers       = $overrides['observers']       ?? [];
        $this->listeners       = $overrides['listeners']       ?? [];
        $this->auditable       = $overrides['auditable']       ?? [];

        $this->migrations      = $overrides['migrations']      ?? [];

        $this->routes          = $overrides['routes']          ?? [];

        $this->views           = $overrides['views']           ?? [];
        $this->translations    = $overrides['translations']    ?? [];

        $this->bladeComponents = $overrides['bladeComponents'] ?? [];
        $this->livewire        = $overrides['livewire']        ?? [];

        $this->publishedFiles  = $overrides['publishedFiles']  ?? [];

        $this->commands        = $overrides['commands']        ?? [];
        $this->schedules       = $overrides['schedules']       ?? [];

        $this->rbac            = $overrides['rbac']            ?? [];
        $this->apis            = $overrides['apis']            ?? [];
        $this->catalogs        = $overrides['catalogs']        ?? [];

        $this->extensions      = $overrides['extensions']      ?? [];
    }

    public function getId(): string
    {
        return Str::slug($this->composerName);
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags, true);
    }

    public function toSummary(): array
    {
        return [
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'version'     => $this->version,
            'type'        => $this->type,
            'tags'        => $this->tags,
        ];
    }

    public function getDetails(): array
    {
        return [
            'name'         => $this->name,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'version'      => $this->version,
            'type'         => $this->type,
            'keywords'     => $this->keywords,
            'tags'         => $this->tags,
            'authors'      => $this->authors,
            'namespace'    => $this->namespace,
            'composerName' => $this->composerName,
            'provider'     => $this->provider,
            'basePath'     => $this->basePath,
            'composerPath' => $this->composerPath,
            'dependencies' => $this->dependencies,
        ];
    }

    public function getVisualDescriptor(): array
    {
        return [
            'id'               => $this->getId(),
            'slug'             => $this->slug,
            'title'            => Str::title(str_replace('-', ' ', $this->slug)),
            'description'      => $this->description,
            'version'          => $this->version,
            'buildVersion'     => $this->buildVersion,
            'type'             => $this->type,
            'vendor'           => $this->vendor ?? null,
            'license'          => $this->license ?? 'proprietary',
            'minimumStability' => $this->minimumStability ?? 'stable',
            'tags'             => $this->tags,
            'authors'          => $this->authors,
            'support'          => $this->support,
            'icon'             => $this->ui['icon'] ?? 'ti ti-box',
            'color'            => $this->ui['color'] ?? 'primary',
            'image'            => $this->ui['image'] ?? null,
            'readme_path'      => $this->ui['readme'] ?? null,
        ];
    }

    private static function buildAttributesFromComposer(array $composer, string $basePath, array $custom = []): array
    {
        $nameParts = explode('/', $composer['name'] ?? 'unknown/module');
        $vendor = $nameParts[0] ?? 'unknown';
        $slug   = $nameParts[1] ?? 'module';

        return [
            'vendor'             => $vendor,
            'slug'               => $slug,
            'name'               => $custom['name'] ?? 'unknown/module',
            'description'        => $custom['description'] ?? ($composer['description'] ?? ''),
            'type'               => $custom['type'] ?? ($composer['type'] ?? 'plugin'),
            'tags'               => $custom['tags'] ?? ($composer['keywords'] ?? []),
            'composerName'       => $composer['name'] ?? 'unknown/module',
            'version'            => $composer['version'] ?? '1.0.0',
            'keywords'           => $composer['keywords'] ?? [],
            'authors'            => $composer['authors'] ?? [],
            'support'            => $composer['support'] ?? [],
            'license'            => $composer['license'] ?? 'proprietary',
            'minimumStability'   => $composer['minimum-stability'] ?? 'stable',
            'buildVersion'       => now()->format('YmdHis'), // Versión de compilación en timestamp
            'namespace'          => array_key_first($composer['autoload']['psr-4'] ?? []) ?? '',
            'provider'           => $composer['extra']['laravel']['providers'][0] ?? null,
            'basePath'           => rtrim($basePath, '/'),
            'composerPath'       => rtrim($basePath, '/') . '/composer.json',
            'dependencies'       => array_keys($composer['require'] ?? []),
            'ui'                 => $custom['ui'] ?? [],
            'configs'            => $custom['configs'] ?? [],
            'componentNamespace' => $custom['componentNamespace'] ?? '',
            'providers'          => $custom['providers'] ?? [],
            'middleware'         => $custom['middleware'] ?? [],
            'aliases'            => $custom['aliases'] ?? [],
            'singletons'         => $custom['singletons'] ?? [],
            'bindings'           => $custom['bindings'] ?? [],
            'macros'             => $custom['macros'] ?? [],
            'observers'          => $custom['observers'] ?? [],
            'listeners'          => $custom['listeners'] ?? [],
            'auditable'          => $custom['auditable'] ?? [],
            'migrations'         => $custom['migrations'] ?? [],
            'routes'             => $custom['routes'] ?? [],
            'views'              => $custom['views'] ?? [],
            'translations'       => $custom['translations'] ?? [],
            'bladeComponents'    => $custom['bladeComponents'] ?? [],
            'livewire'           => $custom['livewire'] ?? [],
            'publishedFiles'     => $custom['publishedFiles'] ?? [],
            'commands'           => $custom['commands'] ?? [],
            'schedules'          => $custom['schedules'] ?? [],
            'rbac'               => $custom['rbac'] ?? [],
            'apis'               => $custom['apis'] ?? [],
            'catalogs'           => $custom['catalogs'] ?? [],
            'extensions'         => $custom['extensions'] ?? [],
        ];
    }

    public static function fromModuleDirectory(string $dirPath): ?KonekoModule
    {
        $file = null;

        if (file_exists($dirPath . '/vuexy-admin.module.php')){
            $file = $dirPath . '/vuexy-admin.module.php';
        }

        if (file_exists($dirPath . '/koneko-vuexy.module.php')){
            $file = $dirPath . '/koneko-vuexy.module.php';
        }

        $result = require $file;

        if ($result instanceof KonekoModule) {
            return $result;
        }

        if (is_array($result)) {
            // Inferir basePath desde el archivo
            $basePath = dirname($file, 2);

            return self::fromModuleDefinition($basePath, $result);
        }

        throw new \RuntimeException("El archivo [$file] no contiene una instancia ni un array válido para definir el módulo.");
    }

    public static function fromModuleDefinition(string $basePath, array $custom = []): self
    {
        $composerPath = rtrim($basePath, '/') . '/composer.json';

        if (!file_exists($composerPath)) {
            throw new \RuntimeException("No se encontró composer.json en: {$composerPath}");
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        if (!is_array($composer)) {
            throw new \RuntimeException("composer.json inválido en: {$composerPath}");
        }

        $attributes = static::buildAttributesFromComposer($composer, realpath($basePath), $custom);

        $module = new self($attributes);

        return $module;
    }







    public static function fromComposerJson(array $composer, string $basePath, array $custom = []): self
    {
        return new self(
            static::buildAttributesFromComposer($composer, $basePath, $custom)
        );
    }

    /**
     * Registra desde archivo PHP que retorna un KonekoModule.
     */
    public static function fromModuleFile(string $file): ?KonekoModule
    {
        if (!file_exists($file)) return null;

        $result = require $file;

        if ($result instanceof KonekoModule) {
            return $result;
        }

        if (is_array($result)) {
            // Inferir basePath desde el archivo
            $basePath = dirname($file, 2);

            return self::fromModuleDefinition($basePath, $result);
        }

        throw new \RuntimeException("El archivo [$file] no contiene una instancia ni un array válido para definir el módulo.");
    }







    /**
     * Obtiene el slug del módulo actualmente activo, basado en la ruta.
     */
    public static function currentSlug(): string
    {
        $module = static::resolveCurrent();

        return $module?->slug ?? 'unknown';
    }

    /**
     * Obtiene el módulo actual, basado en la ruta.
     *
     * @return static|null
     */
    public static function resolveCurrent(): ?self
    {
        $modules = KonekoModuleRegistry::enabled();

        if (empty($modules)) {
            return null;
        }

        $currentRoute = request()?->route()?->getName();

        foreach ($modules as $module) {
            if (str_contains($currentRoute, $module->slug)) {
                return $module;
            }
        }

        // Fallback: el primero que esté activo
        return reset($modules) ?: null;
    }
}
