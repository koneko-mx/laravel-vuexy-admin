<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Bootstrap;

use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;

/**
 * Clase representativa de un módulo del sistema Vuexy Admin
 */
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

    // === Namespace del componente para el sistema de configuración ===
    public string $componentNamespace;

    // === Composer & Autoload ===
    public string $composerName;
    public string $namespace;
    public ?string $provider;
    public string $basePath;
    public string $composerPath;
    public array $dependencies;

    // === Metadatos visuales UI ===
    public array $ui;

    // === Definiciones técnicas del módulo ===
    public array $configs;
    public array $providers;
    public array $middleware;
    public array $aliases;
    public array $singletons;
    public array $bindings;
    public array $macros;
    public array $observers;
    public array $listeners;
    public array $auditable;
    public array $migrations;
    public array $routes;
    public array $views;
    public array $translations;
    public array $bladeComponents;
    public array $livewire;
    public array $publishedFiles;
    public array $commands;
    public array $schedules;
    public array $rbac;
    public array $apis;
    public array $catalogs;
    public array $extensions;
    public array $scopeModels;
    public array $configBlocks;

    public function __construct(array $overrides = [])
    {
        foreach ((new \ReflectionClass($this))->getProperties() as $property) {
            $name = $property->getName();
            $this->$name = $overrides[$name] ?? $this->$name ?? ($property->hasType() && $property->getType()->getName() === 'array' ? [] : null);
        }

        // Defaults no cubiertos
        $this->type             ??= 'plugin';
        $this->version          ??= '1.0.0';
        $this->minimumStability??= 'stable';
        $this->buildVersion     ??= now()->format('YmdHis');
        $this->description      ??= '';
        $this->componentNamespace ??= $overrides['componentNamespace'] ?? '';
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
            'vendor'           => $this->vendor,
            'license'          => $this->license ?? 'proprietary',
            'minimumStability' => $this->minimumStability,
            'tags'             => $this->tags,
            'authors'          => $this->authors,
            'support'          => $this->support,
            'icon'             => $this->ui['icon'] ?? 'ti ti-box',
            'color'            => $this->ui['color'] ?? 'primary',
            'image'            => $this->ui['image'] ?? null,
            'readme_path'      => $this->ui['readme'] ?? null,
        ];
    }

    public static function fromComposerJson(array $composer, string $basePath, array $custom = []): self
    {
        return new self(
            static::buildAttributesFromComposer($composer, $basePath, $custom)
        );
    }

    public static function fromModuleFile(string $file): ?self
    {
        if (!file_exists($file)) return null;

        $result = require $file;

        if ($result instanceof self) return $result;

        if (is_array($result)) {
            return self::fromModuleDefinition(dirname($file, 2), $result);
        }

        throw new \RuntimeException("El archivo [$file] no contiene una instancia ni un array válido para definir el módulo.");
    }

    public static function fromModuleDirectory(string $dirPath): ?self
    {
        foreach (['vuexy-admin.module.php', 'koneko-vuexy.module.php'] as $fileName) {
            $file = $dirPath . '/' . $fileName;
            if (file_exists($file)) {
                return self::fromModuleFile($file);
            }
        }
        return null;
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

        return new self(
            static::buildAttributesFromComposer($composer, realpath($basePath), $custom)
        );
    }

    private static function buildAttributesFromComposer(array $composer, string $basePath, array $custom = []): array
    {
        $nameParts = explode('/', $composer['name'] ?? 'unknown/module');
        $vendor = $nameParts[0] ?? 'unknown';
        $slug   = $nameParts[1] ?? 'module';

        return array_merge([
            'vendor'             => $vendor,
            'slug'               => $slug,
            'name'               => $custom['name'] ?? $composer['name'] ?? 'unknown/module',
            'description'        => $composer['description'] ?? '',
            'type'               => $composer['type'] ?? 'plugin',
            'tags'               => $composer['keywords'] ?? [],
            'composerName'       => $composer['name'] ?? 'unknown/module',
            'version'            => $composer['version'] ?? '1.0.0',
            'keywords'           => $composer['keywords'] ?? [],
            'authors'            => $composer['authors'] ?? [],
            'support'            => $composer['support'] ?? [],
            'license'            => $composer['license'] ?? 'proprietary',
            'minimumStability'   => $composer['minimum-stability'] ?? 'stable',
            'buildVersion'       => now()->format('YmdHis'),
            'namespace'          => array_key_first($composer['autoload']['psr-4'] ?? []) ?? '',
            'provider'           => $composer['extra']['laravel']['providers'][0] ?? null,
            'basePath'           => rtrim($basePath, '/'),
            'composerPath'       => rtrim($basePath, '/') . '/composer.json',
            'dependencies'       => array_keys($composer['require'] ?? []),
        ], $custom);
    }

    public static function currentSlug(): string
    {
        return static::resolveCurrent()?->slug ?? 'unknown';
    }

    public static function resolveCurrent(): ?self
    {
        $modules = KonekoModuleRegistry::enabled();
        $currentRoute = request()?->route()?->getName();

        foreach ($modules as $module) {
            if (str_contains($currentRoute, $module->slug)) {
                return $module;
            }
        }

        return reset($modules) ?: null;
    }
}
