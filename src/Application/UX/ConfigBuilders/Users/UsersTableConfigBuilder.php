<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Users;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Koneko\VuexyAdmin\Support\Builders\AbstractTableConfigBuilder;
use Koneko\VuexyAdmin\Application\Traits\Indexing\HandlesStaticRegistryMerge;

/**
 * UsersTableConfigBuilder
 *
 * ✅ Esta clase ES extensible por otros módulos.
 * Permite a otros componentes (como POS, Billing, etc.) agregar columnas, joins y filtros.
 */
final class UsersTableConfigBuilder extends AbstractTableConfigBuilder
{
    use HandlesStaticRegistryMerge;

    /**
     * Devuelve el modelo de datos.
     */
    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Filtro exclusivo de clientes.
     */
    public function getIndexBaseQuery(): Builder
    {
        return User::query()->isUser();
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        $local = [
            'users.id',
            DB::raw("CONCAT_WS(' ', users.name, users.last_name) AS full_name"),
            'users.email',
            'users.email_verified_at',
            'users.profile_photo_path',
            DB::raw("(SELECT GROUP_CONCAT(`roles`.`name` SEPARATOR '|') AS `roles`
                FROM `model_has_roles` INNER JOIN `roles` ON (`model_has_roles`.`role_id` = `roles`.`id`)
                WHERE`model_has_roles`.`model_id` = users.id AND `model_has_roles`.`model_type` = 'Koneko\\\\VuexyAdmin\\\\Models\\\\User'
                GROUP BY `model_has_roles`.`model_id`
            ) AS roles"),
            'users.status',
            'users.created_by',
            DB::raw("CONCAT_WS(' ', creator.name, creator.last_name) AS creator_name"),
            'creator.email AS creator_email',
            'creator.profile_photo_path AS creator_profile_photo_path',
            'users.created_at',
            'users.updated_at',
        ];

        return static::mergeStaticRegistry('columns', $local);
    }

    /**
     * Devuelve las etiquetas para las columnas del índice.
     */
    public static function getIndexLabels(): array
    {
        $local = [
            'action'     => 'Acciones',
            'full_name'  => 'Usuario',
            'email'      => 'Correo electrónico',
            'email_verified_at' => 'Correo verificado',
            'roles'      => 'Roles',
            'status'     => 'Estado',
            'created_by' => 'Creado por',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
        ];

        return static::sortLabelsByPriority(
            static::mergeStaticRegistry('labels', $local)
        );
    }

    /**
     * Devuelve las prioridades de ordenamiento para las columnas.
     */
    public static function getIndexPriorities(): array
    {
        $local = [
            'action'     => 2,
            'full_name'  => 10,
            'email'      => 20,
            'email_verified_at' => 20,
            'roles'      => 40,
            'status'     => 60,
            'created_by' => 90,
            'created_at' => 90,
            'updated_at' => 90,
        ];

        return static::mergeStaticRegistry('priorities', $local);
    }

    /**
     * Devuelve el formato de visualización para las columnas.
     */
    public static function getIndexFormatters(): array
    {
        $local = [
            'action' => [
                'formatter'     => 'userActionFormatter',
                'onlyFormatter' => true,
            ],
            'full_name' => [
                'formatter' => 'userIdProfileFormatter',
            ],
            'email' => [
                'formatter' => 'emailFormatter',
                'visible'   => false,
            ],
            'email_verified_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
            'roles' => [
                'formatter' => 'userRoleFormatter',
            ],
            'status' => [
                'formatter' => 'booleanStatusFormatter',
                'align' => 'center',
            ],
            'created_by' => [
                'formatter' => 'creatorProfileFormatter',
                'visible'   => false,
            ],
            'created_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
        ];

        return static::mergeStaticRegistry('formatters', $local);
    }

    /**
     * Devuelve los JOINs requeridos por la consulta.
     */
    public static function getIndexJoins(): array
    {
        $local = [
            ["users AS creator", "users.created_by", "=", "creator.id", ["type" => "leftJoin"]],
        ];

        return static::uniqueJoins(
            static::mergeStaticRegistry('joins', $local)
        );
    }

    /**
     * Devuelve los filtros aplicables en la tabla (como búsqueda global).
     */
    public static function getIndexFilters(): array
    {
        $local = [
            'search' => [
                'users.name',
                'users.email'
            ],
        ];

        return static::mergeStaticFilters([
            static::mergeStaticRegistry('filters', $local)
        ]);
    }

    /**
     * Devuelve las rutas para las acciones de la tabla.
     */
    public static function getIndexRoutes(): array
    {
        $local = [
            'admin.user.show'   => route('admin.core.users.users.show', ['user' => ':id']),
            'admin.user.edit'   => route('admin.core.users.users.edit',   ['user' => ':id']),
            'admin.user.delete' => route('admin.core.users.users.delete', ['user' => ':id']),
        ];

        return static::mergeStaticRegistry('routes', $local);
    }

    /**
     * Devuelve las configuraciones del Bootstrap Table.
     *
     */
    public static function getIndexTableConfig(): array
    {
        $local = [
            'export'     => true,
            'fullscreen' => true,
        ];

        return static::mergeStaticRegistry('tableConfig', $local);
    }
}
