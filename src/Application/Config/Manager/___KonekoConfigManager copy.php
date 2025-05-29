<?php

namespace Koneko\VuexyAdmin\Application\Config\Manager;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\Settings\Manager\KonekoSettingManager;

final class ___KonekoConfigManager
{
    protected string $namespace;
    protected string $component;
    protected string $group;
    protected string $section = 'config';
    protected string $subGroup = 'default';
    protected ?string $scope = null;
    protected ?string $scope_id = null;
    protected ?string $key = null;

    public function __construct(
        protected KonekoSettingManager $settings
    ) {}

    public function from(string|object $module, string $qualifiedGroup): static
    {
        [$this->namespace, $this->component] = $this->resolveModuleParts($module);

        $parts = explode('.', $qualifiedGroup);
        $this->group     = $parts[0] ?? 'general';
        $this->subGroup  = $parts[1] ?? 'default';

        return $this;
    }

    public function scopedToUser(int|string $userId): static
    {
        $this->scope    = 'user';
        $this->scope_id = (string) $userId;
        return $this;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;
        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->key = $key;

        // Paso 1: SETTINGS DB
        $value = $this->settings
            ->setNamespace($this->namespace)
            ->setComponent($this->component)
            ->setGroup($this->group)
            ->setSection($this->section)
            ->setSubGroup($this->subGroup)
            ->setKeyName($this->key);

        if ($this->scope && $this->scope_id) {
            $value->setScope($this->scope, $this->scope_id);
        }

        $result = $value->get();
        if (!is_null($result)) return $result;

        // Paso 2: ENV
        $envKey = strtoupper(str_replace('.', '_', $this->qualifiedKey()));
        if (env($envKey) !== null) return env($envKey);

        // Paso 3: CONFIG LOCAL (config/ publicado)
        $configValue = config($this->qualifiedKey());
        if (!is_null($configValue)) return $configValue;

        // Paso 4: CONFIG DEL MÓDULO (registrado en tiempo de boot)
        if (KonekoModuleRegistry::has($this->component)) {
            $moduleConfig = config("{$this->namespace}.{$this->component}");
            return Arr::get($moduleConfig, $this->nestedKey(), $default);
        }

        return $default;
    }

    public function sourceOf(string $key): ?string
    {
        $this->key = $key;

        $checker = $this->settings
            ->setNamespace($this->namespace)
            ->setComponent($this->component)
            ->setGroup($this->group)
            ->setSection($this->section)
            ->setSubGroup($this->subGroup);

        if ($this->scope && $this->scope_id) {
            $checker->setScope($this->scope, $this->scope_id);
        }

        if ($checker->exists($key)) return 'settings';

        $envKey = strtoupper(str_replace('.', '_', $this->qualifiedKey()));
        if (env($envKey) !== null) return 'env';

        if (!is_null(config($this->qualifiedKey()))) return 'config';

        if (KonekoModuleRegistry::has($this->component)) return 'module';

        return null;
    }

    protected function resolveModuleParts(string|object $module): array
    {
        if (is_object($module)) {
            $module = get_class($module);
        }

        if (class_exists($module) && defined("$module::NAMESPACE") && defined("$module::COMPONENT")) {
            return [constant("$module::NAMESPACE"), constant("$module::COMPONENT")];
        }

        if (Str::contains($module, '.')) {
            return explode('.', $module, 2);
        }

        throw new \InvalidArgumentException("No se pudo resolver el módulo desde: {$module}");
    }

    protected function qualifiedKey(): string
    {
        return implode('.', [
            $this->namespace,
            $this->component,
            $this->group,
            $this->subGroup,
            $this->key
        ]);
    }

    protected function nestedKey(): string
    {
        return implode('.', [
            $this->group,
            $this->subGroup,
            $this->key
        ]);
    }
}
