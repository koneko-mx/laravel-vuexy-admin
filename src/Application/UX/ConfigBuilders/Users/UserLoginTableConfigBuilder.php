<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Users;

use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Models\UserLogin;
use Koneko\VuexyAdmin\Support\Builders\AbstractTableConfigBuilder;

/**
 * Configuración de la vista indexada para registros de inicio de sesión de usuarios.
 */
class UserLoginTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve el modelo de datos.
     */
    public function getModelClass(): string
    {
        return UserLogin::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [
            'user_logins.id',
            'user_logins.user_id',
            DB::raw("CONCAT_WS(' ', users.name, users.last_name) AS user_name"),
            'users.profile_photo_path AS user_profile_photo_path',
            'users.email AS user_email',
            'user_logins.device_type',
            'user_logins.ip_address',
            'user_logins.user_agent',
            'user_logins.browser',
            'user_logins.browser_version',
            'user_logins.os',
            'user_logins.os_version',
            'user_logins.country',
            'user_logins.region',
            'user_logins.city',
            'user_logins.lat',
            'user_logins.lng',
            'user_logins.is_proxy',
            'user_logins.logout_at',
            DB::raw("LENGTH(IFNULL(additional_info, '{}')) AS length_of_additional_info"),
            'user_logins.created_at',
            'user_logins.updated_at',
        ];
    }

    /**
     * Etiquetas (labels) específicas para la tabla User Logs.
     */
    public static function getIndexLabels(): array
    {
        return [
            'action'                    => 'Acciones',
            'user_id'                   => 'Usuario',
            'device_type'               => 'Dispositivo',
            'ip_address'                => 'IP',
            'user_agent'                => 'User Agent',
            'browser'                   => 'Navegador',
            'browser_version'           => 'Versión del navegador',
            'os'                        => 'Sistema operativo',
            'os_version'                => 'Versión de S.O.',
            'country'                   => 'País',
            'region'                    => 'Región',
            'city'                      => 'Ciudad',
            'lat'                       => 'Latitud',
            'is_proxy'                  => 'Es proxy',
            'length_of_additional_info' => 'Info. adicional',
            'session_duration'          => 'Duración de sesión',
            'created_at'                => 'Fecha ingreso',
            'logout_at'                 => 'Fecha de cierre',
            'updated_at'                => 'Actualizado',
        ];
    }

    /**
     * JOINs específicos requeridos para esta tabla.
     */
    public static function getIndexJoins(): array
    {
        return [
            ['users', 'users.id', '=', 'user_logins.user_id'],
        ];
    }

    /**
     * Configuración de formatos específicos para User Logs.
     */
    public static function getIndexFormatters(): array
    {
        return [
            'action' => [
                'formatter' => 'userLoginActionFormatter',
                'onlyFormatter' => true,
            ],
            'user_id' => [
                'formatter' => 'userProfileFormatter',
            ],
            'device_type' => [
                'formatter' => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'info'],
                ],
                'align'     => 'center',
            ],
            'ip_address' => [
                'align' => 'center',
            ],
            'user_agent' => [
                'formatter' => 'textXsFormatter',
                'align'     => 'center',
            ],
            'browser' => [
                'formatter' => 'textNowrapFormatter',
                'align'     => 'center',
                'visible' => false
            ],
            'browser_version' => [
                'align' => 'center',
                'visible' => false
            ],
            'os' => [
                'formatter' => 'textNowrapFormatter',
                'align'     => 'center',
                'visible' => false
            ],
            'os_version' => [
                'align' => 'center',
                'visible' => false
            ],
            'is_proxy' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'check']
                ],
                'align'     => 'center',
                'visible' => false
            ],
            'lat' => [
                'formatter' => 'googleMapsFormatter',
                'visible' => false
            ],
            'country' => [
                'visible' => false
            ],
            'region' => [
                'visible' => false
            ],
            'city' => [
                'visible' => false
            ],
            'length_of_additional_info' => [
                'align' => 'center',
                'formatter' => 'bytesToHumanReadable',
                'visible' => false
            ],
            'session_duration' => [
                'formatter' => 'sessionDurationFormatter',
                'align'     => 'center',
            ],
            'created_at' => [
                'formatter' => 'dateLimeFormatter',
                'align'     => 'center',
            ],
            'logout_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Rutas específicas para la tabla User Logs (reutiliza las rutas de User).
     */
    public static function getIndexRoutes(): array
    {
        return [
            'admin.user.show' => route('admin.core.users.users.show', ['user' => ':id']),
        ];
    }

    /**
     * Filtros específicos para búsquedas rápidas.
     */
    public static function getIndexFilters(): array
    {
        return [
            'search' => [
                'users.name',
                'users.email',
                'user_logins.ip_address',
                'user_logins.user_agent'
            ],
        ];
    }
}
