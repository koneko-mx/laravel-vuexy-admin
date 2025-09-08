<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Livewire\Components\Table;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

abstract class AbstractTableComponent extends Component
{
    /** @var array Configuración combinada para Bootstrap Table (columnas, formatters, opciones) */
    public array $bt_datatable = [];

    /** @var string Identificador técnico del modelo */
    public string $tagName;

    /** @var string Nombre legible en singular (ej: 'Producto') */
    public string $singularName;

    /** @var string Nombre legible en plural (ej: 'Productos') */
    public string $pluralName;

    /**
     * Ejecutado cuando se monta el componente Livewire.
     */
    public function mount(): void
    {
        $this->setupModelMetadata();
        $this->setupDataTable();
    }

    /**
     * Obtiene una instancia del modelo definido en el ConfigBuilder.
     *
     * @throws \LogicException
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        if ($builderClass = $this->configBuilderClass()) {
            $modelClass = app($builderClass)->getModelClass();

            return app($modelClass);
        }

        throw new \LogicException('Debe definirse el método configBuilderClass() que retorne una clase con getModelClass().');
    }

    /**
     * Establece valores de metadatos del modelo (tagName, nombres legibles).
     *
     * @return void
     */
    protected function setupModelMetadata(): void
    {
        $model = $this->getModelInstance();

        $this->tagName      = $model->getTagName();
        $this->singularName = $model->getSingularName();
        $this->pluralName   = $model->getPluralName();
    }

    /**
     * Configura la tabla
     *
     * @return void
     */
    protected function setupDataTable(): void
    {
        $model         = $this->getModelInstance();
        $builderClass  = $this->configBuilderClass();

        // Configuración base por defecto
        $baseConfig = [
            'sortName'             => $model->getSortColumn(),
            'sortOrder'            => $model->getDefaultSortOrder(),
            'exportFileName'       => $model->getPluralName(),
            'cookie'               => false,
            'exportWithDatetime'   => true,
            'showFullscreen'       => false,
            'showPaginationSwitch' => false,
            'showRefresh'          => false,
            'pagination'           => true,
            'search'               => false,
            'showToggle'           => true,
            'showColumns'          => true,
            'showExport'           => true,
        ];

        $columns = [];
        $formatters  = [];

        if ($builderClass) {
            /** @var \Koneko\VuexyAdmin\Support\Builders\AbstractTableConfigBuilder $builder */
            $builder = app($builderClass);

            $baseConfig = array_merge($baseConfig, $builder->getIndexTableConfig());
            $columns    = $builder->getIndexLabels();
            $formatters = $builder->getIndexFormatters();
        }

        // Rutas
        $routes = $builder->getIndexRoutes();

        $this->bt_datatable = [
            ...$baseConfig,
            'header'     => $columns,
            'formatters' => $formatters,
            'routes'     => $routes,
        ];
    }

    /**
     * Retorna el nombre de la clase AbstractTableConfigBuilder que define la configuración.
     *
     * @return class-string|null
     */
    protected function configBuilderClass(): ?string
    {
        return null;
    }

    /**
     * Permite aplicar filtros adicionales en el query base.
     * Se puede sobrescribir en la subclase.
     *
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters(array $criteria = [])
    {
        return $this->getModelInstance()::query();
    }
}
