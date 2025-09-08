<?php

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Cache\Builders\SettingCacheKeyBuilder;
use Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry;
use Koneko\VuexyAdmin\Application\Traits\System\Context\HasBaseContextValidator;

trait HasBaseContext
{
    use HasBaseContextValidator;

    /** Si se requieren respuestas como array en algunos managers */
    protected bool $asArray = false;

    /**
     * Contexto base para generación de claves y filtros.
     */
    protected array $context = [
        'namespace'   => null,
        'environment' => null,
        'component'   => null,
        'scope'       => null,
        'scope_id'    => null,
        'group'       => null,
        'section'     => null,
        'sub_group'   => null,
        'key_name'    => null,
    ];

    // ==================== Factory helpers ====================

    public static function fromArray(array $context): static
    {
        return static::make()->setContextArray($context);
    }

    /**
     * Inyecta arreglo de contexto (solo campos presentes).
     */
    public function setContextArray(array $context): static
    {
        if (isset($context['environment'])) { $this->environment($context['environment']); }
        if (isset($context['component']))   { $this->component($context['component']); }
        if (isset($context['group']))       { $this->group($context['group']); }
        if (isset($context['section']))     { $this->section($context['section']); }
        if (isset($context['sub_group']))   { $this->subGroup($context['sub_group']); }
        if (isset($context['key_name']))    { $this->keyName($context['key_name']); }

        if (array_key_exists('scope', $context) || array_key_exists('scope_id', $context)) {
            $this->scope($context['scope'] ?? null, $context['scope_id'] ?? null);
        }

        return $this;
    }

    /**
     * Atajo para definir group.section.sub_group usando una ruta con puntos.
     * Faltantes se rellenan con 'default'.
     */
    public function ctx(string $path): static
    {
        [$g, $s, $sub] = array_pad(explode('.', $path, 3), 3, 'default');
        return $this->context($g, $s, $sub);
    }

    /**
     * Define group/section/sub_group con validación de slug.
     */
    public function context(?string $group, ?string $section, ?string $subGroup = 'default'): static
    {
        $this->context['group']     = $group    ? $this->validateSlug('group', $group, 24)       : null;
        $this->context['section']   = $section  ? $this->validateSlug('section', $section, 24)   : null;
        $this->context['sub_group'] = $subGroup ? $this->validateSlug('sub_group', $subGroup, 24): null;
        return $this;
    }

    // ======================= Context Base =========================

    /** Define namespace (interno, normalmente leído desde config). */
    public function namespace(?string $namespace): static
    {
        $this->context['namespace'] = $namespace === null
            ? null
            : $this->validateSlug('namespace', $namespace, 8);

        return $this;
    }

    /** Define environment. Preferido por la interfaz pública. */
    public function environment(?string $environment = null): static
    {
        $environment ??= app()->environment();
        $this->context['environment'] = $this->validateSlug('environment', $environment, 16);
        return $this;
    }

    /** Define component. */
    public function component(string $component): static
    {
        $this->context['component'] = $this->validateSlug('component', $component, 24);
        return $this;
    }

    // ======================= Scope =========================

    /**
     * Define scope y scope_id. Pasar null como $scope para limpiar el contexto.
     */
    public function scope(Model|string|null $scope, ?int $scopeId = null): static
    {
        // Limpiar
        if ($scope === null) {
            $this->context['scope'] = null;
            $this->context['scope_id'] = null;
            return $this;
        }

        if ($scope instanceof Model) {
            return $this->withScopeFromModel($scope);
        }

        // scope como string
        $this->context['scope'] = $this->validateSlug('scope', (string) $scope, 24);
        $this->context['scope_id'] = $scopeId;
        return $this;
    }

    /** Solo modifica el scope_id manteniendo el scope actual. */
    public function scopeId(?int $scopeId): static
    {
        $this->context['scope_id'] = $scopeId;
        return $this;
    }

    /** Setea scope/scope_id a partir de un modelo registrado. */
    public function withScopeFromModel(Model $model): static
    {
        $context = ScopeRegistry::resolveScopeFromModel($model);

        if (!$context) {
            throw new \InvalidArgumentException('El modelo proporcionado no está asociado a ningún scope registrado.');
        }

        $this->context['scope']    = $this->validateScope($context['scope']);
        $this->context['scope_id'] = $context['scope_id'];
        return $this;
    }

    // ======================= Segmentos de contexto =========================

    public function group(string $group): static
    {
        $this->context['group'] = $this->validateSlug('group', $group, 24);
        return $this;
    }

    public function section(string $section): static
    {
        $this->context['section'] = $this->validateSlug('section', $section, 24);
        return $this;
    }

    public function subGroup(string $subGroup): static
    {
        $this->context['sub_group'] = $this->validateSlug('sub_group', $subGroup, 24);
        return $this;
    }

    public function keyName(string $keyName): static
    {
        $this->context['key_name'] = $this->validateKeyName($keyName);
        return $this;
    }

    // ======================= Output helpers =========================

    public function asArray(bool $state = true): static
    {
        $this->asArray = $state;
        return $this;
    }

    /**
     * Genera la clave calificada. Si se pasa $keyName no muta el contexto.
     */
    public function getQualifiedKey(?string $keyName = null): string
    {
        $this->validateScopeContext($this->context['scope'], $this->context['scope_id']);

        $nameToUse = $keyName ?? ($this->context['key_name'] ?? null);
        $this->requireKeyName($nameToUse);

        return SettingCacheKeyBuilder::build(
            $this->context['namespace'],
            $this->context['environment'],
            $this->context['scope'],
            $this->context['scope_id'],
            $this->context['component'],
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $nameToUse
        );
    }

    /** Devuelve instancia del modelo de scope si es resoluble. */
    public function getScopeModel(): ?Model
    {
        return ($this->context['scope'] && $this->context['scope_id'])
            ? ScopeRegistry::getModelInstance($this->context['scope'], $this->context['scope_id'])
            : null;
    }

    // ======================= Flags booleanos de estado (útiles para validaciones) =========================

    public function hasComponentContext(): bool
    {
        return (bool) ($this->context['namespace'] && $this->context['environment'] && $this->context['component']);
    }

    public function hasGroupContext(): bool
    {
        return (bool) ($this->context['group'] && $this->context['section'] && $this->context['sub_group']);
    }

    public function hasBaseContext(): bool
    {
        return $this->hasComponentContext() && !empty($this->context['key_name']);
    }

    public function hasScopeContext(): bool
    {
        return (bool) ($this->context['scope'] && $this->context['scope_id']);
    }

    public function hasFullContext(): bool
    {
        return $this->hasComponentContext() && $this->hasGroupContext() && !empty($this->context['key_name']);
    }
}
