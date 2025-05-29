<?php

namespace Koneko\VuexyAdmin\Application\Config\Builder;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Settings\KonekoSettingManager;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;

class KonekoConfigResolverService
{
    protected KonekoSettingManager $settings;

    public function __construct(KonekoSettingManager $settings)
    {
        $this->settings = $settings;

        $this
            ->setNamespace(CoreModule::NAMESPACE)
            ->setEnvironment();
    }

    /**
     * Obtiene una clave con jerarquía: settings > .env > config local > config de módulo.
     */
    public function get(string $qualifiedKey, mixed $default = null): mixed
    {
        // Extraer segmentos para contexto (namespace.component.group.section.subgroup.key)
        $parts = explode('.', $qualifiedKey);

        if (count($parts) < 3) {
            return config($qualifiedKey, $default); // fallback si es un config no calificado
        }

        $namespace = $parts[0];
        $component = $parts[1];
        $group     = $parts[2] ?? 'general';
        $section   = 'config';
        $subGroup  = $parts[3] ?? 'default';
        $key       = $parts[4] ?? end($parts);

        // 1. SETTINGS (BD, sección config)
        $value = $this->settings
            ->setNamespace($namespace)
            ->setComponent($component)
            ->setGroup($group)
            ->setSection($section)
            ->setSubGroup($subGroup)
            ->setKeyName($key)
            ->get();

        if (!is_null($value)) return $value;

        // 2. ENV (si existe como override)
        $envKey = strtoupper(str_replace('.', '_', $qualifiedKey));
        if (env($envKey) !== null) {
            return env($envKey);
        }

        // 3. CONFIG LOCAL (config/koneko/{component}.php)
        $value = config($qualifiedKey);
        if (!is_null($value)) return $value;

        // 4. CONFIG DEL MÓDULO (registrado por orquestador)
        $moduleKey = "$namespace.$component";
        if ($module = KonekoModuleRegistry::get($component)) {
            $moduleConfig = config($moduleKey);
            $nestedKey = implode('.', array_slice($parts, 2));
            return Arr::get($moduleConfig, $nestedKey, $default);
        }

        return $default;
    }

    /**
     * Resuelve una clave usando una clase declarativa del módulo como contexto.
     *
     * @param  string $moduleClass  Ej: CoreModule::class
     * @param  string $qualifiedKey Ej: 'core.menu.cache.ttl'
     * @param  mixed  $default
     */
    public function fromModuleClass(string $moduleClass, string $qualifiedKey, mixed $default = null): mixed
    {
        if (!class_exists($moduleClass)) {
            throw new \InvalidArgumentException("Clase de módulo no encontrada: {$moduleClass}");
        }

        if (!defined("$moduleClass::NAMESPACE") || !defined("$moduleClass::COMPONENT")) {
            throw new \InvalidArgumentException("La clase de módulo debe definir las constantes NAMESPACE y COMPONENT.");
        }

        $namespace = constant("$moduleClass::NAMESPACE");
        $component = constant("$moduleClass::COMPONENT");

        // Prefijar si aún no lo está (ej: 'core.menu.cache.ttl' -> 'koneko.core.menu.cache.ttl')
        if (!str_starts_with($qualifiedKey, "$namespace.")) {
            $qualifiedKey = "$namespace.$qualifiedKey";
        }

        return $this->get($qualifiedKey, $default);
    }


    /**
     * Devuelve la fuente de la clave (para inspección o debugging).
     */
    public function sourceOf(string $qualifiedKey): ?string
    {
        $parts = explode('.', $qualifiedKey);

        if (count($parts) < 3) return null;

        $namespace = $parts[0];
        $component = $parts[1];
        $group     = $parts[2] ?? 'general';
        $section   = 'config';
        $subGroup  = $parts[3] ?? 'default';
        $key       = $parts[4] ?? end($parts);

        $has = $this->settings
            ->setNamespace($namespace)
            ->setComponent($component)
            ->setGroup($group)
            ->setSection($section)
            ->setSubGroup($subGroup)
            ->exists($key);

        if ($has) return 'settings';

        $envKey = strtoupper(str_replace('.', '_', $qualifiedKey));
        if (env($envKey) !== null) {
            return 'env';
        }

        if (!is_null(config($qualifiedKey))) {
            return 'config';
        }

        if (KonekoModuleRegistry::has($component)) {
            return 'module';
        }

        return null;
    }
}
