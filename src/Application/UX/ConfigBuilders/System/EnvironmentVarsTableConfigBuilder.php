<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System;

use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Support\Builders\Table\AbstractTableConfigBuilder;

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
            'settings.key',
            'settings.namespace',
            'settings.environment',
            'settings.component',
            'settings.module',
            'settings.scope',
            'settings.scope_id',
            'settings.group',
            'settings.section',
            'settings.sub_group',
            'settings.key_name',
            'settings.is_system',
            'settings.is_sensitive',
            'settings.is_file',
            'settings.is_encrypted',
            'settings.is_config',
            'settings.is_editable',
            'settings.is_track_usage',
            'settings.is_should_cache',
            'settings.is_active',
            'settings.mime_type',
            'settings.file_name',
            'settings.encryption_algorithm',
            'settings.encryption_key',
            'settings.encryption_rotated_at',
            'settings.expires_at',
            'settings.usage_count',
            'settings.last_used_at',
            'settings.cache_ttl',
            'settings.cache_expires_at',
            'settings.description',
            'settings.hint',
            'settings.value_string',
            'settings.value_integer',
            'settings.value_boolean',
            'settings.value_float',
            DB::raw('LENGTH(settings.value_text) as value_text_length'),
            DB::raw('OCTET_LENGTH(settings.value_binary) AS value_binary_length'),

            'settings.created_at',
            'creator.id AS creator_id',
            DB::raw('CONCAT_WS(" ", creator.name, creator.last_name) AS creator_name'),
            'creator.email AS creator_email',
            'creator.profile_photo_path AS creator_profile_photo_path',

            'settings.updated_at',
            'updater.id AS updater_id',
            DB::raw('CONCAT_WS(" ", updater.name, updater.last_name) AS updater_name'),
            'updater.email AS updater_email',
            'updater.profile_photo_path AS updater_profile_photo_path',
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
            'namespace'           => 'Namespace',
            'environment'         => 'Entorno',
            'component'           => 'Componente',
            'module'              => 'Modulo',
            'scope'               => 'Scope',
            'scope_id'            => 'Scope ID',
            'group'               => 'Grupo',
            'section'             => 'Sección',
            'sub_group'           => 'Sub Grupo',
            'key_name'            => 'Nombre',
            'is_system'           => 'Sistema',
            'is_sensitive'        => 'Sensible',
            'is_file'             => 'Archivo',
            'is_encrypted'        => 'Encriptado',
            'is_config'           => 'Config',
            'is_editable'         => 'Editable',
            'is_track_usage'      => 'Seguimiento',
            'is_should_cache'     => 'Cache',
            'is_active'           => 'Activo',
            'mime_type'           => 'Tipo MIME',
            'file_name'           => 'Archivo',
            'encryption_algorithm'=> 'Algoritmo',
            'encryption_key'      => 'Clave',
            'encryption_rotated_at'=> 'Rotado',
            'expires_at'          => 'Expira',
            'usage_count'         => 'Uso',
            'last_used_at'        => 'Ultimo Uso',
            'cache_ttl'           => 'Cache TTL',
            'cache_expires_at'    => 'Cache Expira',
            'description'         => 'Descripcion',
            'hint'                => 'Ayuda',
            'value_string'        => 'Texto',
            'value_integer'       => 'Entero',
            'value_boolean'       => 'Booleano',
            'value_float'         => 'Decimal',
            'value_text_length'   => 'Texto Largo',
            'value_binary_length' => 'Archivo (Tamaño)',
            'created_at'          => 'Creado',
            'creator_id'          => 'Creado por',
            'updated_at'          => 'Actualizado',
            'updater_id'          => 'Modificado por',
        ];
    }

    /**
     * Devuelve los JOINs requeridos para la consulta.
     */
    public static function getIndexJoins(): array
    {
        return [
            ["users AS creator", "settings.created_by", "=", "creator.id", ["type" => "leftJoin"]],
            ["users AS updater", "settings.updated_by", "=", "updater.id", ["type" => "leftJoin"]],
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
            'creator_id' => [
                'formatter' => 'creatorProfileFormatter',
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
            'creator_id' => [
                'formatter' => 'creatorProfileFormatter',
                'visible'   => false,
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'updater_id' => [
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
