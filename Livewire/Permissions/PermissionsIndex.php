<?php

namespace Koneko\VuexyAdmin\Livewire\Permissions;

use Koneko\VuexyAdmin\Livewire\Table\AbstractIndexComponent;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * Listado de Permisos, extiende de la clase base AbstractIndexComponent
 * para reutilizar la lógica de configuración y renderizado de tablas.
 */
class PermissionsIndex extends AbstractIndexComponent
{
    /**
     * Define la clase del modelo a usar en este Index.
     *
     * @return string
     */
    protected function model(): string
    {
        return Permission::class;
    }

    /**
     * Retorna las columnas de la tabla.
     *
     * @return array
     */
    protected function columns(): array
    {
        return [
            'action'         => 'Acciones',
            'name'           => 'Nombre del Permiso',
            'group_name'     => 'Grupo',
            'sub_group_name' => 'Subgrupo',
            'action'         => 'Acción',
            'guard_name'     => 'Guard',
            'created_at'     => 'Creado',
            'updated_at'     => 'Modificado',
        ];
    }

    /**
     * Retorna el formato para cada columna.
     *
     * @return array
     */
    protected function format(): array
    {
        return [
            'action' => [
                'formatter'     => 'storeActionFormatter',
                'onlyFormatter' => true,
            ],
            'name' => [
                'switchable' => false,
            ],
            'created_at' => [
                'formatter' => 'whitespaceNowrapFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
            'updated_at' => [
                'formatter' => 'whitespaceNowrapFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Sobrescribe la configuración base de la tabla.
     *
     * @return array
     */
    protected function bootstraptableConfig(): array
    {
        return array_merge(parent::bootstraptableConfig(), [
            'sortName'            => 'name',
            'exportFileName'      => 'Permisos',
            'showFullscreen'      => false,
            'showPaginationSwitch'=> false,
            'showRefresh'         => false,
            'pagination'          => false,
        ]);
    }

    /**
     * Retorna la vista a renderizar por este componente.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.permissions.index';
    }
}
