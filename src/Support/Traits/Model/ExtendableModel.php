<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Model;

use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model\ModelExtensionRegistry;

trait ExtendableModel
{
    /**
     * Obtiene los atributos extendidos para un tipo específico (fillable, cast, etc.)
     */
    public function getExtendedAttributes(string $type): array
    {
        $attributes = [];

        // A: Atributos locales definidos en propiedades como extraFillable, extraHidden, etc.
        $prop = 'extra' . ucfirst($type);
        if (property_exists($this, $prop)) {
            $attributes = array_merge($attributes, $this->{$prop});
        }

        // B: Atributos registrados globalmente desde el proyecto
        $attributes = array_merge($attributes, ModelExtensionRegistry::getAttributesFor(static::class, $type));

        return $attributes;
    }

    // ========== OVERRIDES COMUNES ==========

    public function getFillable(): array
    {
        return array_unique(array_merge(
            $this->fillable ?? [],
            $this->getExtendedAttributes('fillable')
        ));
    }

    public function getHidden(): array
    {
        return array_unique(array_merge(
            $this->hidden ?? [],
            $this->getExtendedAttributes('hidden')
        ));
    }

    public function getAppends(): array
    {
        return array_unique(array_merge(
            $this->appends ?? [],
            $this->getExtendedAttributes('appends')
        ));
    }

    public function getCasts(): array
    {
        $parentCasts = method_exists(get_parent_class($this), 'getCasts')
            ? parent::getCasts()
            : [];

        return array_merge(
            $parentCasts,
            $this->casts ?? [],
            $this->getExtendedAttributes('cast')
        );
    }

    public function getAuditInclude(): array
    {
        return array_unique(array_merge(
            $this->auditInclude ?? [],
            $this->getExtendedAttributes('auditInclude')
        ));
    }
}
