<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

use Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry;

trait HasBaseContextValidator
{
    protected function validateSlug(string $field, string $value, int $maxLength): string
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            throw new \InvalidArgumentException("El valor '{$value}' de '{$field}' debe ser un slug válido.");
        }

        if (strlen($value) > $maxLength) {
            throw new \InvalidArgumentException("El valor de '{$field}' excede {$maxLength} caracteres.");
        }

        return $value;
    }

    protected function validateKeyName(string $keyName): string
    {
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $keyName)) {
            throw new \InvalidArgumentException("El valor '{$keyName}' de 'keyName' debe ser un slug válido.");
        }

        if (strlen($keyName) > 24) {
            throw new \InvalidArgumentException("El valor de 'keyName' excede 24 caracteres.");
        }

        return $keyName;
    }

    protected function validateScopeContext(?string $scope, ?int $scopeId): void
    {
        if ($scope === null && $scopeId === null) {
            return;
        }

        if ($scopeId && $scope === null) {
            throw new \LogicException("Se definió scopeId sin indicar el tipo de scope.");
        }

        $this->validateScope($scope);

        if ($scope && $scopeId === null) {
            throw new \InvalidArgumentException("Scope '{$scope}' requiere un scopeId.");
        }
    }

    protected function validateScope(?string $scope): ?string
    {
        if ($scope && !ScopeRegistry::isRegistered($scope)) {
            throw new \InvalidArgumentException("Scope '{$scope}' no registrado en ScopeRegistry.");
        }

        return $scope;
    }

    protected function requireKeyName(): void
    {
        if (empty($this->context['key_name'] ?? null)) {
            throw new \InvalidArgumentException("No se ha definido 'key_name' en contexto.");
        }
    }
}
