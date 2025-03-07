<?php

namespace Koneko\VuexyAdmin\Livewire\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Clase base abstracta para la creación de componentes tipo "Index" con Livewire.
 *
 * Provee una estructura general para:
 *  - Configurar y renderizar tablas con Bootstrap Table.
 *  - Definir columnas y formatos de manera estándar.
 *  - Manejar búsquedas, filtros, o catálogos necesarios.
 *  - Centralizar la lógica de montaje (mount).
 *
 * @package Koneko\VuexyAdmin\Livewire\Table
 */
abstract class AbstractIndexComponent extends Component
{
    /**
     * Configuración principal para la tabla con Bootstrap Table.
     *
     * @var array
     */
    public $bt_datatable = [];

    /**
     * Tag identificador del componente, derivado del modelo.
     *
     * @var string
     */
    public $tagName;

    /**
     * Nombre singular del modelo (para mensajes, etiquetado, etc.).
     *
     * @var string
     */
    public $singularName;

    /**
     * Identificador único del formulario (vinculado al Offcanvas o Modal).
     *
     * @var string
     */
    public $formId;

    /**
     * Método para obtener la instancia del modelo asociado.
     *
     * Debe retornarse una instancia (o la clase) del modelo Eloquent que maneja este Index.
     *
     * @return Model|string
     */
    abstract protected function model(): string;

    /**
     * Define las columnas (header) de la tabla. Este array se fusionará
     * o se inyectará en la configuración principal $bt_datatable.
     *
     * @return array
     */
    abstract protected function columns(): array;

    /**
     * Define el formato (formatter) de las columnas.
     *
     * @return array
     */
    abstract protected function format(): array;

    /**
     * Retorna la ruta de la vista Blade que renderizará el componente.
     *
     * @return string
     */
    abstract protected function viewPath(): string;

    /**
     * Método que define la configuración base del DataTable.
     * Aquí puedes poner ajustes comunes (exportFileName, paginación, etc.).
     *
     * @return array
     */
    protected function bootstraptableConfig(): array
    {
        return [
            'sortName'            => 'id',        // Campo por defecto para ordenar
            'exportFileName'      => 'Listado',   // Nombre de archivo para exportar
            'showFullscreen'      => false,
            'showPaginationSwitch'=> false,
            'showRefresh'         => false,
            'pagination'          => false,
            // Agrega aquí cualquier otra configuración por defecto que uses
        ];
    }

    /**
     * Se ejecuta al montar el componente Livewire.
     * Configura $tagName, $singularName, $formId y $bt_datatable.
     *
     * @return void
     */
    public function mount(): void
    {
        // Obtenemos el modelo
        $model = $this->model();
        if (is_string($model)) {
            // Si se retornó la clase en abstract protected function model(),
            // instanciamos manualmente
            $model = new $model;
        }

        // Usamos las propiedades definidas en el modelo
        // (tagName, singularName, etc.), si existen en el modelo.
        // Ajusta nombres según tu convención.
        $this->tagName      = $model->tagName ?? Str::snake(class_basename($model));
        $this->singularName = $model->singularName ?? class_basename($model);
        $this->formId       = Str::kebab($this->tagName) . '-form';

        // Inicia la configuración principal de la tabla
        $this->setupDataTable();
    }

    /**
     * Combina la configuración base de la tabla con las columnas y formatos
     * definidos en las clases hijas.
     *
     * @return void
     */
    protected function setupDataTable(): void
    {
        $baseConfig = $this->bootstraptableConfig();

        $this->bt_datatable = array_merge($baseConfig, [
            'header' => $this->columns(),
            'format' => $this->format(),
        ]);
    }

    /**
     * Renderiza la vista definida en viewPath().
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view($this->viewPath());
    }

    /**
     * Ejemplo de método para la lógica de filtrado que podrías sobreescribir en la clase hija.
     *
     * @param  array  $criteria
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters($criteria = [])
    {
        // Aplica tu lógica de filtros, búsquedas, etc.
        // La clase hija podría sobrescribir este método o llamarlo desde su propia lógica.
        $query = $this->model()::query();

        // Por ejemplo:
        /*
        if (!empty($criteria['store_id'])) {
            $query->where('store_id', $criteria['store_id']);
        }
        */

        return $query;
    }
}
