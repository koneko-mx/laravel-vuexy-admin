<?php

namespace Koneko\VuexyAdmin\Application\Cache;

/**
 * 📊 Gestor de Cache del Ecosistema Koneko
 * Soporte para múltiples niveles (core, componente, grupo), drivers mixtos y tagging.
 * Compatible con redis, memcached, file y database.
 */
class KonekoCacheManager
{
    private string $namespace;
    private string $component;
    private string $group;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Establece el contexto de la caché.
     */
    public function setContext(string $component, string $group): static
    {
        return $this
            ->setNamespace($this->namespace)
            ->setComponent($component)
            ->setGroup($group);
    }

    /**
     * Establece el namespace de la caché.
     */
    public function setNamespace(string $namespace): static
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $namespace)) {
            throw new \InvalidArgumentException("El namespace '{$namespace}' debe ser un slug válido.");
        }

        $this->namespace = strtolower($namespace);

        return $this;
    }

    /**
     * Establece el componente de la caché.
     */
    public function setComponent(string $component): static
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $component)) {
            throw new \InvalidArgumentException("El componente '{$component}' debe ser un slug válido.");
        }

        $this->component = strtolower($component);

        return $this;
    }

    /**
     * Establece el grupo de la caché.
     */
    public function setGroup(string $group): static
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $group)) {
            throw new \InvalidArgumentException("El grupo '{$group}' debe ser un slug válido.");
        }

        $this->group = strtolower($group);

        return $this;
    }

    public function currentNamespace(): string
    {
        return $this->namespace;
    }

    public function currentComponent(): string
    {
        return $this->component;
    }

    public function currentGroup(): string
    {
        return $this->group;
    }

    /**
     * Genera una clave calificada para la caché.
     */
    public function key(string $suffix): string
    {
        if (empty($this->component) || empty($this->group)) {
            throw new \LogicException("Component and group must be set before generating a cache key.");
        }

        return "{$this->path()}.{$suffix}";
    }

    /**
     * Obtiene un valor de configuración.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return config($this->key($key), $default);
    }

    /**
     * Obtiene el tiempo de vida (TTL) de la caché.
     */
    public function ttl(): int
    {
        return (int) (
            config("{$this->namespace}.{$this->component}.{$this->group}.ttl") ??
            config("{$this->namespace}.{$this->component}.cache.ttl") ??
            config("{$this->namespace}.cache.ttl", 3600)
        );
    }

    /**
     * Obtiene el estado de habilitación de la caché.
     */
    public function enabled(): bool
    {
        return (bool) (
            config("{$this->namespace}.{$this->component}.{$this->group}.enabled") ??
            config("{$this->namespace}.{$this->component}.cache.enabled") ??
            config("{$this->namespace}.cache.enabled", true)
        );
    }

    /**
     * Determina si se debe depurar la caché.
     */
    public function shouldDebug(): bool
    {
        return (bool) $this->config('debug', false);
    }

    /**
     * Obtiene el driver de caché.
     */
    public function driver(): string
    {
        return config('cache.default');
    }

    /**
     * Registra los valores por defecto en la configuración.
     */
    public function registerDefaults(): void
    {
        if (! config()->has($this->key('ttl'))) {
            config()->set($this->key('ttl'), 3600);
        }

        if (! config()->has($this->key('enabled'))) {
            config()->set($this->key('enabled'), true);
        }
    }

    /**
     * Obtiene la ruta de la caché.
     */
    public function path(): string
    {
        return "{$this->namespace}.{$this->component}.{$this->group}";
    }

    /**
     * Información extendida de depuración.
     */
    public function info(): array
    {
        return [
            'component' => $this->component,
            'group'     => $this->group,
            'enabled'   => $this->enabled(),
            'ttl'       => $this->ttl(),
            'driver'    => $this->driver(),
            'debug'     => $this->shouldDebug(),
        ];
    }
}
