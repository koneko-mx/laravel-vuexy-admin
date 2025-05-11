<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Factories;

trait HasDynamicFactoryExtenders
{
    /**
     * Busca todos los métodos como `getDefinitionXyz()` y los ejecuta para recolectar definiciones.
     */
    protected function collectExtensionDefinitions(string $prefix = 'getDefinition'): array
    {
        return collect(get_class_methods($this))
            ->filter(fn($m) => str_starts_with($m, $prefix) && $m !== $prefix)
            ->reduce(fn($carry, $method) => array_merge($carry, $this->$method() ?? []), []);
    }
}
