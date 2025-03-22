<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;;

use Koneko\VuexyAdmin\Livewire\Table\AbstractIndexComponent;
use Koneko\VuexyAdmin\Models\Setting;

/**
 * Listado de Configuraciones (settings), extiende la clase base AbstractIndexComponent
 * para reutilizar la lógica de configuración y renderizado de tablas.
 */
class GlobalSettingsIndex extends AbstractIndexComponent
{
    /**
     * Define la clase o instancia del modelo a usar.
     *
     * @return string
     */
    protected function model(): string
    {
        return Setting::class;
    }

    /**
     * Retorna las columnas (header) de la tabla.
     * Se eligen las columnas más relevantes para mantener una interfaz mobile-first.
     *
     * @return array
     */
    protected function columns(): array
    {
        return [
            'action'      => 'Acciones',
            'key'         => 'Clave',
            'category'    => 'Categoría',
            'user_fullname' => 'Usuario',
            'created_at'  => 'Creado',
        ];
    }

    /**
     * Retorna el formato (formatter) para cada columna.
     * Se aplican formatters para resaltar la información y se establecen propiedades de alineación y visibilidad.
     *
     * @return array
     */
    protected function format(): array
    {
        return [
            'action' => [
                'formatter'     => 'settingActionFormatter',
                'onlyFormatter' => true,
            ],
            'key' => [
                'formatter'  => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'primary'],
                ],
                'align'      => 'center',
                'switchable' => false,
            ],
            'category' => [
                'switchable' => false,
            ],
            'user_fullname' => [
                'switchable' => false,
            ],
            'created_at' => [
                'formatter' => 'whitespaceNowrapFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Sobrescribe la configuración base de la tabla para ajustar
     * la vista y funcionalidades específicas del catálogo.
     *
     * @return array
     */
    protected function bootstraptableConfig(): array
    {
        return array_merge(parent::bootstraptableConfig(), [
            'sortName'             => 'key',
            'exportFileName'       => 'Configuración',
            'showFullscreen'       => false,
            'showPaginationSwitch' => false,
            'showRefresh'          => false,
            'pagination'           => false,
        ]);
    }

    /**
     * Retorna la vista a renderizar para este componente.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.global-settings.index';
    }
}
