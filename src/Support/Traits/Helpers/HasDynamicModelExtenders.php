<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Helpers;

/**
 * Permite que el modelo recolecte dinámicamente atributos como `fillable`, `casts`, `auditInclude`, etc.,
 * desde métodos definidos en los Traits que sigue la convención `getXyzAttributes()`.
 *
 * Por ejemplo:
 * - getFillableAttributes()
 * - getCastAttributes()
 * - getAuditAttributes()
 */
trait HasDynamicModelExtenders
{
    /**
     * Recolecta todos los métodos definidos que inician con el prefijo dado (ej: `getFillable`).
     */
    protected function collectDynamicAttributes(string $prefix): array
    {
        logger("Running collectDynamicAttributes: $prefix");

        return collect(get_class_methods($this))
            ->filter(function ($method) use ($prefix) {
                $valid = str_starts_with($method, $prefix)
                    && $method !== $prefix
                    && (new \ReflectionMethod($this, $method))->getDeclaringClass()->getName() !== \Illuminate\Database\Eloquent\Model::class;

                logger("Checking method: $method → valid: " . json_encode($valid));

                return $valid;
            })
            ->reduce(function ($carry, $method) {
                $result = $this->{$method}();
                return is_array($result) ? array_merge($carry, $result) : $carry;
            }, []);
    }

    /**
     * Devuelve los atributos `casts` combinando los del padre (si existe) con los de Traits.
     */
    public function getCasts(): array
    {
        $parentCasts = method_exists(get_parent_class($this), 'getCasts')
            ? parent::getCasts()
            : [];

        return array_merge(
            $parentCasts,
            $this->collectDynamicAttributes('getCast')
        );
    }
}
