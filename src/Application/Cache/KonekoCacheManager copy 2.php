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
    private string $subGroup;

    public function __construct(string $namespace, string $component = 'core')
    {
        $this->namespace = $namespace;
        $this->component = $component;
    }

    public function setContext(string $component, string $group, string $subGroup): static
    {
        return $this
            ->setComponent($component)
            ->setGroup($group)
            ->setSubGroup($subGroup);
    }

    public function setNamespace(string $namespace): static
    {
        $this->validateSlug('namespace', $namespace);
        
        $this->namespace = strtolower($namespace);

        return $this;
    }

    public function setComponent(string $component): static
    {
        $this->validateSlug('component', $component);
        
        $this->component = strtolower($component);

        return $this;
    }

    public function setGroup(string $group): static
    {
        $this->validateSlug('group', $group);
        
        $this->group = strtolower($group);

        return $this;
    }

    public function setSubGroup(string $subGroup): static
    {
        $this->validateSlug('subGroup', $subGroup);
        
        $this->subGroup = strtolower($subGroup);

        return $this;
    }


    private function ensureContext(): void
    {
        foreach (['component', 'group', 'subGroup'] as $context) {
            if (empty($this->$context)) {
                throw new \LogicException("Debe establecer {$context} antes de generar una clave de caché.");
            }
        }
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

    public function currentSubGroup(): string
    {
        return $this->subGroup;
    }




    public function fullKey(string $suffix): string
    {
        return "{$this->path()}.{$suffix}";
    }
    


    public function key(string $suffix): string
    {
        $this->ensureContext();

        return "{$this->path()}.{$suffix}";
    }

    public function config(string $key, mixed $default = null): mixed
    {
        return config($this->key($key), $default);
    }



    public function ttl(): int
    {
        return (int) (
            config("{$this->namespace}.{$this->component}.{$this->group}.{$this->subGroup}.ttl")
            ?? config("{$this->namespace}.{$this->component}.{$this->group}.ttl")
            ?? config("{$this->namespace}.{$this->component}.cache.ttl")
            ?? config("{$this->namespace}.cache.ttl", 3600)
        );
    }

    public function enabled(): bool
    {
        return (bool) (
            config("{$this->namespace}.{$this->component}.{$this->group}.{$this->subGroup}.enabled")
            ?? config("{$this->namespace}.{$this->component}.{$this->group}.enabled")
            ?? config("{$this->namespace}.{$this->component}.cache.enabled")
            ?? config("{$this->namespace}.cache.enabled", true)
        );
    }

    public function shouldDebug(): bool
    {
        return (bool) $this->config('debug', false);
    }

    public function driver(): string
    {
        return config('cache.default');
    }

    public function path(): string
    {
        return "{$this->namespace}.{$this->component}.{$this->group}.{$this->subGroup}";
    }

    public function info(): array
    {
        return [
            'namespace' => $this->namespace,
            'component' => $this->component,
            'group'     => $this->group,
            'subGroup'  => $this->subGroup,
            'enabled'   => $this->enabled(),
            'ttl'       => $this->ttl(),
            'driver'    => $this->driver(),
            'debug'     => $this->shouldDebug(),
        ];
    }

    private function validateSlug(string $field, string $value): void
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            throw new \InvalidArgumentException("El valor de '{$field}' debe ser un slug válido.");
        }
    }

    private function ensureContext(): void
    {
        if (empty($this->component) || empty($this->group)) {
            throw new \LogicException("Debe establecer component y group antes de generar una clave de caché.");
        }
    }
}
