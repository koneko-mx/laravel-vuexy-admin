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
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $model */
        $model = $this->settingModel;
        return $model::query();
    }

    /**
     * Aplica filtros de contexto al builder.
     *
     * @param  Builder  $query
     * @param  array    $filters  claves: namespace, environment, scope, scope_id, component, group, section, sub_group, key_name
     * @return Builder
     */
    protected function applyContextFilters(Builder $query, array $filters = []): Builder
    {
        $ctx = $this->context;

        $query
            ->when(($filters['namespace']   ?? false), fn($q) => $q->where('namespace',   $ctx['namespace']   ?? null))
            ->when(($filters['environment'] ?? false), fn($q) => $q->where('environment', $ctx['environment'] ?? null))
            ->when(($filters['scope']       ?? false), fn($q) => $q->where('scope',       $ctx['scope']       ?? null))
            ->when(($filters['scope_id']    ?? false), fn($q) => $q->where('scope_id',    $ctx['scope_id']    ?? null))
            ->when(($filters['component']   ?? false), fn($q) => $q->where('component',   $ctx['component']   ?? null))
            ->when(($filters['group']       ?? false), fn($q) => $q->where('group',       $ctx['group']       ?? null))
            ->when(($filters['section']     ?? false), fn($q) => $q->where('section',     $ctx['section']     ?? null))
            ->when(($filters['sub_group']   ?? false), fn($q) => $q->where('sub_group',   $ctx['sub_group']   ?? null))
            ->when(($filters['key_name']    ?? false), fn($q) => $q->where('key_name',    $ctx['key_name']    ?? null));

        // Estado/expiración por defecto
        if (!($this->includeDisabled ?? false)) {
            $query->where('is_active', true);
        }

        if (!($this->includeExpired ?? false)) {
            $query->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
        }

        return $query;
    }

    /** Contexto completo excepto key_name */
    protected function query(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
            'section'     => true,
            'sub_group'   => true,
        ]);
    }

    /** Contexto completo + key_name */
    protected function queryByKey(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
            'section'     => true,
            'sub_group'   => true,
            'key_name'    => true,
        ]);
    }

    /** Por grupo (filtra hasta group) */
    protected function queryByGroup(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
        ]);
    }

    /** Por sección (filtra hasta section) */
    protected function queryBySection(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
            'section'     => true,
        ]);
    }

    /** Por sub_group (filtra hasta sub_group) */
    protected function queryBySubGroup(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'scope'       => true,
            'scope_id'    => true,
            'component'   => true,
            'group'       => true,
            'sub_group'   => true,
        ]);
    }

    /** Por componente (útil para deleteComponent, listados globales por componente) */
    protected function queryByComponent(): Builder
    {
        return $this->applyContextFilters($this->newQuery(), [
            'namespace'   => true,
            'environment' => true,
            'component'   => true,
        ]);
    }

    /** Lee value resuelto por key del contexto actual */
    protected function queryForValue(mixed $default = null): mixed
    {
        return $this->queryByKey()->first()?->value ?? $default;
    }
}
