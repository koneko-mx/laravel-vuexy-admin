<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Indexing;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

/**
 * Trait para acceder metadatos desde instancias del modelo en configuraciones de índice.
 */
trait HandlesModelMetadata
{
    abstract public function getModelClass(): string;

    /**
     * Retorna instancia del modelo validando el uso del trait obligatorio.
     */
    public function getModelInstance(): Model|HasVuexyModelMetadata
    {
        $model = app($this->getModelClass());

        if (!in_array(HasVuexyModelMetadata::class, class_uses_recursive($model))) {
            throw new \LogicException(
                "El modelo " . get_class($model) . " debe usar el trait HasVuexyModelMetadata."
            );
        }

        return $model;
    }

    // A partir de ahora simplemente delegas a los métodos del trait del modelo:

    public function getTableName(): string
    {
        return $this->getModelInstance()->getTable();
    }

    public function getTagName(): string
    {
        return $this->getModelInstance()->getTagName();
    }

    public function getSingularName(): string
    {
        return $this->getModelInstance()->getSingularName();
    }

    public function getPluralName(): string
    {
        return $this->getModelInstance()->getPluralName();
    }

    public function getSortColumn(): string
    {
        return $this->getModelInstance()->getSortColumn();
    }

    public function getDefaultSortOrder(): string
    {
        return $this->getModelInstance()->getDefaultSortOrder();
    }
}
