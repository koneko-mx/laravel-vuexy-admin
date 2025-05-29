<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait reutilizable para construir queries dinámicas basadas en contexto.
 * Funciona tanto para Settings como para modelos relacionados a Scope.
 */
trait HasContextQueryBuilder
{
    protected function newQuery(): Builder
    {
        return $this->settingModel::query();
    }

    /**
     * Aplica filtros de contexto dinámico a un query builder.
     *
     * @param Builder $query
     * @param array $filters (claves: namespace, environment, scope, scope_id, component, group, section, sub_group, key_name)
     *
     * @return Builder
     */
    protected function applyContextFilters(Builder $query, array $filters = []): Builder
    {
        $ctx = $this->context;

        $query = $query
            ->when($filters['namespace']   ?? false, fn($q) => $q->where('namespace', $ctx['namespace'] ?? null))
            ->when($filters['environment'] ?? false, fn($q) => $q->where('environment', $ctx['environment'] ?? null))
            ->when($filters['scope']       ?? false, fn($q) => $q->where('scope', $ctx['scope'] ?? null))
            ->when($filters['scope_id']    ?? false, fn($q) => $q->where('scope_id', $ctx['scope_id'] ?? null))
            ->when($filters['component']   ?? false, fn($q) => $q->where('component', $ctx['component'] ?? null))
            ->when($filters['group']       ?? false, fn($q) => $q->where('group', $ctx['group'] ?? null))
            ->when($filters['section']     ?? false, fn($q) => $q->where('section', $ctx['section'] ?? null))
            ->when($filters['sub_group']   ?? false, fn($q) => $q->where('sub_group', $ctx['sub_group'] ?? null))
            ->when($filters['key_name']    ?? false, fn($q) => $q->where('key_name', $ctx['key_name'] ?? null));

        // Solo aplicar estado si no se anula
        if (!($this->includeDisabled ?? false)) {
            $query->where('is_active', true);
        }

        if (!($this->includeExpired ?? false)) {
            $query->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        }

        return $query;
    }

    protected function query(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace' => true,
            'environment' => true,
            'scope' => true,
            'scope_id' => true,
            'component' => true,
            'group' => true,
            'section' => true,
            'sub_group' => true,
        ]);
    }

    protected function queryByKey(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace' => true,
            'environment' => true,
            'scope' => true,
            'scope_id' => true,
            'component' => true,
            'group' => true,
            'section' => true,
            'sub_group' => true,
            'key_name' => true,
        ]);
    }

    protected function queryByGroup(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'scope' => true,
            'scope_id' => true,
            'component' => true,
            'group' => true,
        ]);
    }

    protected function queryBySection(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'scope' => true,
            'scope_id' => true,
            'component' => true,
            'group' => true,
            'section' => true,
        ]);
    }

    protected function queryBySubGroup(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'scope' => true,
            'scope_id' => true,
            'component' => true,
            'group' => true,
            'sub_group' => true,
        ]);
    }

    protected function queryForValue(mixed $default = null): mixed
    {
        return $this->queryByKey()->first()?->value ?? $default;
    }
}
