<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Builders\Table;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilderContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Koneko\VuexyAdmin\Application\Queries\BootstrapTableQueryBuilder;
use Koneko\VuexyAdmin\Application\Traits\ConfigBuilder\HandlesModelMetadata;
use Koneko\VuexyAdmin\Application\Traits\ConfigBuilder\{HandlesIndexColumns, HandlesIndexLabels, HandlesTableConfig};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

/**
 * Clase base moderna para configurar listados de modelos en el ERP.
 * Ahora expone una configuración completa (columns/joins/filters/grouping/sort/limits)
 * que consume el nuevo BootstrapTableQueryBuilder.
 */
abstract class AbstractTableConfigBuilder
{
    use HandlesModelMetadata;
    use HandlesIndexColumns, HandlesIndexLabels, HandlesTableConfig;

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
    public function getIndexBaseQuery(): QueryBuilderContract
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
     * Orden por defecto (fallback para allowed_sort/default_sort).
     */
    public function getSortColumn(): string { return 'id'; }
    public function getDefaultSortOrder(): string { return 'desc'; }

    /**
     * Declaración de JOINS (por defecto ninguno).
     * @return array<int, array>
     */
    public static function getIndexJoins(): array { return []; }

    /**
     * Declaración de filtros (DSL) – por defecto ninguno.
     */
    public static function getIndexFilters(): array { return []; }

    /**
     * Declaración de agrupado/agregados – por defecto ninguno.
     * Ej: ['by' => [...], 'aggregates' => [...], 'having' => [...]]
     */
    public static function getIndexGrouping(): array { return []; }

    /**
     * Whitelist de campos/alias para ordenar desde el front.
     * Acepta lista simple ["title","status"] o mapa ["title"=>"posts.title"].
     */
    public static function getIndexAllowedSort(): array { return []; }

    /** Límites de paginación */
    public function getDefaultLimit(): int { return 10; }
    public function getMaxLimit(): int { return 1000; }

    /** Devuelve sort por defecto (columna + orden) */
    public function getDefaultSort(): array
    {
        return [
            'column' => $this->getSortColumn(),
            'order'  => $this->getDefaultSortOrder(),
        ];
    }

    /**
     * Construye la configuración completa para el QueryBuilder.
     */
    public function buildIndexConfig(): array
    {
        return [
            'columns'       => static::getIndexColumns(),
            'joins'         => static::getIndexJoins(),
            'filters'       => static::getIndexFilters(),
            'grouping'      => static::getIndexGrouping(),
            'allowed_sort'  => static::getIndexAllowedSort(),
            'default_sort'  => $this->getDefaultSort(),
            'default_limit' => $this->getDefaultLimit(),
            'max_limit'     => $this->getMaxLimit(),
        ];
    }

    /**
     * Retorna el QueryBuilder de Bootstrap Table usando la config declarada.
     */
    public function getQueryBuilder(Request $request): BootstrapTableQueryBuilder
    {
        return new BootstrapTableQueryBuilder(
            $request,
            $this->getIndexBaseQuery(),
            $this->buildIndexConfig()
        );
    }
}
