<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System;

use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Models\SecurityEvent;
use Koneko\VuexyAdmin\Support\Builders\Table\AbstractTableConfigBuilder;

class SecurityEventsTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve el modelo de datos.
     */
    public function getModelClass(): string
    {
        return SecurityEvent::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [
            'security_events.id',
            'security_events.user_id',
            DB::raw("CONCAT_WS(' ', users.name, users.last_name) AS user_name"),
            'users.email AS user_email',
            'users.profile_photo_path AS user_profile_photo_path',
            'security_events.event_type',
            'security_events.status',
            'security_events.ip_address',
            'security_events.user_agent',
            'security_events.device_type',
            'security_events.browser',
            'security_events.browser_version',
            'security_events.os',
            'security_events.os_version',
            'security_events.country',
            'security_events.region',
            'security_events.city',
            'security_events.lat',
            'security_events.lng',
            'security_events.is_proxy',
            'security_events.url',
            'security_events.http_method',
            'security_events.payload',
            'security_events.created_at',
            'security_events.updated_at',
        ];
    }

    /**
     * Devuelve las etiquetas (labels) para las columnas.
     */
    public static function getIndexLabels(): array
    {
        return [
            'id'                     => 'ID',
            'user_name'              => 'Usuario',
            'user_email'             => 'Correo',
            'event_type'             => 'Evento',
            'status'                 => 'Estatus',
            'ip_address'             => 'Dirección IP',
            'user_agent'             => 'User Agent',
            'device_type'            => 'Dispositivo',
            'browser'                => 'Navegador',
            'browser_version'        => 'Versión Navegador',
            'os'                     => 'Sistema Operativo',
            'os_version'             => 'Versión SO',
            'country'                => 'País',
            'region'                 => 'Región',
            'city'                   => 'Ciudad',
            'lat'                    => 'Geolocalización',
            'is_proxy'               => 'Proxy',
            'url'                    => 'URL',
            'http_method'            => 'Método HTTP',
            'payload'                => 'Payload',
            'created_at'             => 'Fecha Creación',
            'updated_at'             => 'Fecha Actualización',
        ];
    }

    /**
     * Devuelve los JOINs requeridos por la consulta.
     */
    public static function getIndexJoins(): array
    {
        return [
            ["users", "users.id", "=", "security_events.user_id", ["type" => "leftJoin"]],
        ];
    }


    /**
     * Devuelve los filtros aplicables en la tabla (como búsqueda global).
     */
    public static function getIndexFilters(): array
    {
        return [
            'search' => [
                'users.name',
                'users.last_name',
                'users.email',
                'security_events.event_type',
                'security_events.ip_address',
            ],
        ];
    }

    /**
     * Devuelve la configuración de formatos y visibilidad para cada columna.
     */
    public static function getIndexFormatters(): array
    {
        return [
            'user_name' => [
                'formatter' => 'userProfileFormatter',
            ],
            'user_email' => [
                'formatter' => 'emailFormatter',
                'visible' => false,
            ],
            'event_type' => [
                'formatter' => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'warning'],
                ],
                'align' => 'center',
            ],
            'status' => [
                'align' => 'center',
                'visible' => false,
            ],
            'ip_address' => [
                'formatter' => 'textNowrapFormatter',
                'align' => 'center',
            ],
            'device_type' => [
                'formatter' => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'primary'],
                ],
                'align' => 'center',
            ],
            'browser' => [
                'align' => 'center',
                'visible' => false,
            ],
            'browser_version' => [
                'align' => 'center',
                'visible' => false,
            ],
            'os' => [
                'align' => 'center',
                'visible' => false,
            ],
            'os_version' => [
                'align' => 'center',
                'visible' => false,
            ],
            'country' => [
                'align' => 'center',
            ],
            'region' => [
                'align' => 'center',
                'visible' => false,
            ],
            'city' => [
                'align' => 'center',
                'visible' => false,
            ],
            'lat' => [
                'formatter' => 'googleMapsFormatter',
                'visible' => false,
            ],
            'is_proxy' => [
                'formatter' => 'dynamicBooleanFormatter',
                'visible' => false,
                'align' => 'center',
            ],
            'url' => [
                'formatter' => 'textNowrapFormatter',
                'visible' => false,
                'align' => 'center',
            ],
            'http_method' => [
                'formatter' => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'info'],
                ],
                'align' => 'center',
            ],
            'payload' => [
                'formatter' => 'textXsFormatter',
                'visible' => false,
                'visible' => false,
            ],
            'created_at' => [
                'formatter' => 'dateBadgeBlueFormatter',
                'align' => 'center',
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align' => 'center',
                'visible' => false,
            ],
        ];
    }

    /**
     * Devuelve las rutas del CRUD para cada fila.
     */
    public static function getIndexRoutes(): array
    {
        return [
            'admin.user.show' => route('admin.core.users.users.show', ['user' => ':id']),
        ];
    }

    /**
     * Devuelve configuraciones adicionales del Bootstrap Table.
     */
    public static function getIndexTableConfig(): array
    {
        return [
            'search' => true,
        ];
    }
}
