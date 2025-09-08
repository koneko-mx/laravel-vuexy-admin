<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

use Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry;

trait HasBaseContextValidator
{
    /**
     * Valida un slug en minúsculas con [a-z0-9_-] y longitud máxima.
     */
    protected function validateSlug(string $field, string $value, int $maxLength): string
    {
        if (!preg_match('/^[a-z0-9\-._]+$/', $value)) {
            throw new \InvalidArgumentException("El valor '{$value}' de '{$field}' debe ser un slug válido (a-z, 0-9, '-', '_').");
        }

        if (strlen($value) > $maxLength) {
            throw new \InvalidArgumentException("El valor de '{$field}' excede {$maxLength} caracteres.");
        }

        return $value;
    }

    /**
     * Valida el nombre de clave (key_name) y su longitud.
     */
    protected function validateKeyName(string $keyName): string
    {
        if (!preg_match('/^[a-z0-9\._-]+$/', $keyName)) {
            throw new \InvalidArgumentException("El valor '{$keyName}' de 'keyName' debe contener solo minúsculas, números, puntos o guiones.");
        }

        if (strlen($keyName) > 32) {
            throw new \InvalidArgumentException("El valor de 'keyName' excede 32 caracteres.");
        }

        return $keyName;
    }

    /**
     * Valida coherencia de scope y scope_id.
     */
    protected function validateScopeContext(?string $scope, ?int $scopeId): void
    {
        // Caso sin scope
        if ($scope === null && $scopeId === null) {
            return;
        }

        if ($scopeId !== null && $scope === null) {
            throw new \LogicException('Se definió scope_id sin indicar el tipo de scope.');
        }

        $this->validateScope($scope);

        if ($scope !== null && $scopeId === null) {
            throw new \InvalidArgumentException("Scope '{$scope}' requiere un scope_id.");
        }
    }

    /**
     * Verifica que el scope esté registrado en el ScopeRegistry.
     */
    protected function validateScope(?string $scope): ?string
    {
        if ($scope && !ScopeRegistry::isRegistered($scope)) {
            throw new \InvalidArgumentException("Scope '{$scope}' no registrado en ScopeRegistry.");
        }

        return $scope;
    }

    /**
     * Asegura que exista un key_name válido ya sea provisto o en el contexto.
     */
    protected function requireKeyName(?string $keyName = null): void
    {
        $candidate = $keyName ?? ($this->context['key_name'] ?? null);
        if (empty($candidate)) {
            throw new \InvalidArgumentException("No se ha definido 'key_name' en el contexto.");
        }
        $this->validateKeyName($candidate);
    }
}
