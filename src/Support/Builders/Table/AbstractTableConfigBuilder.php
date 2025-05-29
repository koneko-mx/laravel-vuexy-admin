<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Builders;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Traits\Indexing\HandlesModelMetadata;
use Koneko\VuexyAdmin\Application\Traits\Indexing\{HandlesIndexColumns,HandlesIndexLabels,HandlesTableConfig};
use Koneko\VuexyAdmin\Application\Traits\Indexing\HandlesQueryBuilder;
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

/**
 * Clase base moderna para configurar listados de modelos en el ERP.
 * Modularizada por Traits, desacoplada de BootstrapTable.
 */
abstract class AbstractTableConfigBuilder
{
    use HandlesModelMetadata;
    use HandlesIndexColumns,
        HandlesIndexLabels,
        HandlesTableConfig;
    use HandlesQueryBuilder;

    /**
     * Clase del modelo principal.
     */
    abstract public function getModelClass(): string;

    /**
     * Retorna instancia del modelo validando el uso del trait obligatorio.
     */
    public function getModelInstance(): Model
    {
        $model = app($this->getModelClass());

        if (!in_array(HasVuexyModelMetadata::class, class_uses_recursive($model))) {
            throw new \LogicException("El modelo ".get_class($model)." debe usar el trait HasVuexyModelMetadata.");
        }

        return $model;
    }

    /**
     * Base query que se usa para construir listados.
     */
    public function getIndexBaseQuery(): Builder
    {
        return $this->getModelClass()::query();
    }

    /**
     * Alias semántico para nombre de tabla del modelo.
     */
    public function getTableName(): string
    {
        return $this->getModelInstance()->getTable();
    }

    /**
     * Por defecto, ordena por columna ID descendente.
     */
    public function getSortColumn(): string { return 'id'; }
    public function getDefaultSortOrder(): string { return 'desc'; }

}
