<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Rbac;

use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Models\PermissionMeta;
use Koneko\VuexyAdmin\Support\Builders\Table\AbstractTableConfigBuilder;

class PermissionsTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve la clase del module principal.
     */
    public function getModelClass(): string
    {
        return PermissionMeta::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [
            'permissions.id',
            'permissions.name',
            DB::raw("(SELECT GROUP_CONCAT(roles.name SEPARATOR '|') as roles FROM role_has_permissions INNER JOIN roles ON (role_has_permissions.role_id = roles.id) WHERE role_has_permissions.permission_id = permissions.id) as roles"),
            'permissions.label',
            'permissions.ui_metadata',
            'permissions.action',
            'permissions.guard_name',
            'permissions.created_at',
            'permissions.updated_at',
        ];
    }

    /**
     * Devuelve las etiquetas legibles (labels) para las columnas.
     * Cortas, claras y adaptadas a México.
     */
    public static function getIndexLabels(): array
    {
        return [
            'action'         => 'Acciones',
            'name'           => 'Permiso',
            'roles'          => 'Roles',
            'label'          => 'Etiqueta',
            'ui_metadata'    => 'Metadata',
            'action'         => 'Acción',
            'guard_name'     => 'Guardia',
            'created_at'     => 'Creado',
            'updated_at'     => 'Actualizado',
        ];
    }

    /**
     * Devuelve los filtros aplicables en la búsqueda.
     */
    public static function getIndexFilters(): array
    {
        return [
            'search' => [
                'permissions.name',
                'roles',
            ],
        ];
    }

    /**
     * Devuelve los formatters por columna.
     * Aquí se define visibilidad, estilos y formateo visual.
     */
    public static function getIndexFormatters(): array
    {
        return [
            'action' => [
                'formatter' => 'settingsActionFormatter',
                'onlyFormatter' => true,
            ],
            'name' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'roles' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'created_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
        ];
    }

    /**
     * Configuración avanzada del componente Bootstrap Table.
     * Enfocada en visibilidad técnica con buena UX.
     */
    public static function getIndexTableConfig(): array
    {
        return [
            'search' => true,
            'fixedNumber' => 2,
        ];
    }
}
