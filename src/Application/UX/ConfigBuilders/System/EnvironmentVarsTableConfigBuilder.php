<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System;

use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Builders\AbstractTableConfigBuilder;

/**
 * Configuración de la vista indexada para parámetros y configuraciones (`settings`) del sistema.
 * Soporta múltiples tipos de valores, usuario asignado y metadatos del archivo adjunto (si aplica).
 */
class EnvironmentVarsTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve la clase del module principal.
     */
    public function getModelClass(): string
    {
        return Setting::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [
            'settings.id',
            'settings.key',
            'settings.module',
            'settings.user_id',
            DB::raw('CONCAT_WS(" ", users.name, users.last_name) AS user_name'),
            'users.email AS user_email',
            'users.profile_photo_path AS user_profile_photo_path',
            'settings.value_string',
            'settings.value_integer',
            'settings.value_boolean',
            'settings.value_float',
            DB::raw('LENGTH(settings.value_text) as value_text_length'),
            DB::raw('OCTET_LENGTH(settings.value_binary) AS value_binary_length'),
            'settings.mime_type',
            'settings.file_name',
            'settings.created_at',
            'settings.updated_at',
            'settings.updated_by',
            DB::raw('CONCAT_WS(" ", creator.name, creator.last_name) AS creator_name'),
            'creator.email AS creator_email',
            'creator.profile_photo_path AS creator_profile_photo_path',
        ];
    }

    /**
     * Devuelve las etiquetas legibles (labels) para las columnas.
     * Cortas, claras y adaptadas a México.
     */
    public static function getIndexLabels(): array
    {
        return [
            'action'              => 'Acciones',
            'key'                 => 'Clave',
            'module'              => 'modulo',
            'user_id'             => 'Usuario',
            'value_string'        => 'Texto corto',
            'value_integer'       => 'Entero',
            'value_boolean'       => 'Activo',
            'value_float'         => 'Decimal',
            'value_text_length'   => 'Texto largo',
            'value_binary_length' => 'Archivo (Tamaño)',
            'mime_type'           => 'Tipo MIME',
            'file_name'           => 'Archivo',
            'created_at'          => 'Creado',
            'created_by'          => 'Creado por',
            'updated_at'          => 'Actualizado',
            'created_by'          => 'Modificado por',
        ];
    }

    /**
     * Devuelve los JOINs requeridos para la consulta.
     */
    public static function getIndexJoins(): array
    {
        return [
            ["users", "users.id", "=", "settings.user_id", ["type" => "leftJoin"]],
            ["users AS creator", "settings.created_by", "=", "creator.id", ["type" => "leftJoin"]],
        ];
    }

    /**
     * Devuelve los filtros aplicables en la búsqueda.
     */
    public static function getIndexFilters(): array
    {
        return [
            'search' => [
                'settings.key',
                'settings.module',
                'users.name',
                'users.last_name',
                'users.email',
                'creator.name',
                'creator.last_name',
                'value_string',
                'mime_type',
                'file_name',
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
            'module' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'user_id' => [
                'formatter' => 'userProfileFormatter',
            ],
            'value_boolean' => [
                'formatter' => 'dynamicBooleanFormatter',
            ],
            'value_text_length' => [
                'align' => 'center',
                'formatter' => 'bytesToHumanReadable',
            ],
            'value_binary_length' => [
                'align' => 'center',
                'formatter' => 'bytesToHumanReadable',
            ],
            'created_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'created_by' => [
                'formatter' => 'creatorProfileFormatter',
                'visible'   => false,
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'updated_by' => [
                'formatter' => 'updaterProfileFormatter',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Devuelve las rutas CRUD utilizadas en los botones de acción.
     * En este caso no se usan, pero se deja estructura base.
     */
    public static function getIndexRoutes(): array
    {
        return [
            'admin.user.show' => route('admin.core.users.users.show', ['user' => ':id']),
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
            'showRefresh' => true,
            'showFullscreen' => true,
            'fixedNumber' => 2,
        ];
    }
}
