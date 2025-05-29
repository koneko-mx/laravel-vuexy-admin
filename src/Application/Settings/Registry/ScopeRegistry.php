<?php

namespace Koneko\VuexyAdmin\Application\Settings\Registry;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Support\Traits\Auth\HasResolvableUser;

final class ScopeRegistry
{
    use HasResolvableUser;

    protected static array $registeredScopes = [];

    /**
     * Registra un nuevo tipo de scope.
     */
    public static function register(string $scope, string $modelClass): void
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("El modelo '{$modelClass}' debe extender Illuminate\\Database\\Eloquent\\Model.");
        }

        static::$registeredScopes[$scope] = $modelClass;
    }

    /**
     * Verifica si un scope está registrado.
     */
    public static function isRegistered(string $scope): bool
    {
        return isset(static::$registeredScopes[$scope]) || in_array($scope, static::$registeredScopes);
    }

    /**
     * Devuelve la clase del modelo asociado a un scope.
     */
    public static function modelFor(string $scope): ?string
    {
        return static::$registeredScopes[$scope] ?? null;
    }

    /**
     * Devuelve una instancia de modelo para el scope e ID dados.
     */
    public static function getModelInstance(string $scope, int|string $id): ?Model
    {
        $modelClass = static::modelFor($scope);

        if (!$modelClass || !class_exists($modelClass)) {
            return null;
        }

        return $modelClass::find($id);
    }

    /**
     * Retorna todos los scopes registrados.
     */
    public static function all(): array
    {
        return static::$registeredScopes;
    }

    /**
     * Descubre el contexto de scope de un modelo.
     */
    public static function resolveScopeFromModel(Model $model): ?array
    {
        foreach (static::$registeredScopes as $scope => $modelClass) {
            if ($model instanceof $modelClass) {
                return [
                    'scope'    => $scope,
                    'scope_id' => $model->getKey(),
                ];
            }
        }

        return null;
    }

    /**
     * Descubre el contexto scope del usuario actual.
     */
    public static function guessUserScopeContext(Authenticatable|int|null|false $user = null): ?array
    {
        $userId = static::resolveUserId($user);

        if (!$userId || !static::isRegistered('user')) {
            return null;
        }

        return [
            'scope'    => 'user',
            'scope_id'=> $userId,
        ];
    }
}
