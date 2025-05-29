<?php

namespace Koneko\VuexyAdmin\Application\Traits\System\Context;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\Cache\Builders\SettingCacheKeyBuilder;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry;
use Koneko\VuexyAdmin\Application\Traits\System\Context\HasBaseContextValidator;
use Koneko\VuexyAdmin\Support\Traits\Auth\HasResolvableUser;

trait HasBaseContext
{
    use HasResolvableUser;
    use HasBaseContextValidator;

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

    // ==================== Factory ====================

    public static function fromArray(array $context): static
    {
        return static::make()->setContextArray($context);
    }

    public static function fromRequest(?Request $request = null): static
    {
        return static::make()->resolveFromRequest($request);
    }

    public function resolveFromRequest(?Request $request = null): static
    {
        $request ??= request();

        if ($model = $request->route()?->parameter('model')) {
            $this->withScopeFromModel($model);

        } elseif (Auth::check()) {
            $this->setUser(Auth::user());
        }

        $this->setEnvironment();

        return $this;
    }

    public function context(string $group, ?string $section = null, ?string $subGroup = null): static
    {
        $this->context['group'] = $this->validateSlug('group', $group, 16);

        if ($section) {
            $this->context['section'] = $this->validateSlug('section', $section, 16);
        }

        if ($subGroup) {
            $this->context['sub_group'] = $this->validateSlug('sub_group', $subGroup, 16);
        }

        return $this;
    }

    public function setContextArray(array $context): static
    {
        $this->reset();

        if (isset($context['namespace']))   $this->setNamespace($context['namespace']);
        if (isset($context['environment'])) $this->setEnvironment($context['environment']);
        if (isset($context['component']))   $this->setComponent($context['component']);
        if (isset($context['group']))       $this->setGroup($context['group']);
        if (isset($context['section']))     $this->setSection($context['section']);
        if (isset($context['sub_group']))   $this->setSubGroup($context['sub_group']);
        if (isset($context['key_name']))    $this->setKeyName($context['key_name']);

        if (isset($context['scope'], $context['scope_id'])) {
            $this->setScope($context['scope'], $context['scope_id']);
        }

        return $this;
    }

    // ======================= Context Base =========================

    public function setNamespace(string $namespace): static
    {
        $this->context['namespace'] = $this->validateSlug('namespace', $namespace, 8);
        return $this;
    }

    public function setEnvironment(?string $environment = null): static
    {
        $this->context['environment'] = $environment
            ? $this->validateSlug('environment', $environment, 7)
            : app()->environment();
        return $this;
    }

    public function setComponent(string $component): static
    {
        if (class_exists($component)) {
            return $this->loadModuleClass($component);
        }

        $this->context['component'] = $this->validateSlug('component', $component, 16);
        return $this;
    }

    /**
     * Carga el contexto de un módulo usando una clase declarativa.
     */
    protected function loadModuleClass(string $moduleClass): static
    {
        if (!defined("$moduleClass::NAMESPACE") || !defined("$moduleClass::COMPONENT")) {
            throw new \InvalidArgumentException("La clase de módulo debe definir las constantes NAMESPACE y COMPONENT.");
        }

        $namespace = constant("$moduleClass::NAMESPACE");
        $component = constant("$moduleClass::COMPONENT");

        return $this
            ->setNamespace($namespace)
            ->setComponent($component);
    }

    // ======================= Scope =========================

    public function setScope(Model|string|false $scope, int|null|false $scopeId = false): static
    {
        if ($scope === false) {
            $this->context['scope']    = null;
            $this->context['scope_id'] = null;
            return $this;
        }

        // Limpiamos el scopeId si es false
        if ($scopeId === false) {
            $scopeId = null;
        }

        // Obtenemos el scope y el scope_id de un modelo
        if ($scope instanceof Model) {
            $this->withScopeFromModel($scope);

        // Si el scope es una cadena, validamos el slug
        } else {
            $this->context['scope'] = $this->validateScope($scope);
        }

        // Si el scopeId no es false, lo asignamos
        if ($scopeId !== false) {
            $this->context['scope_id'] = $scopeId;
        }

        return $this;
    }

    public function setScopeId(?int $scopeId): static
    {
        $this->context['scope_id'] = $scopeId;
        return $this;
    }

    public function setUser(Authenticatable|int|null|false $user): static
    {
        $this->context['scope']    = 'user';
        $this->context['scope_id'] = $this->resolveUserId($user);
        return $this;
    }

    public function withScopeFromModel(Model $model): static
    {
        $context = ScopeRegistry::resolveScopeFromModel($model);

        if (!$context) {
            throw new \InvalidArgumentException('El modelo proporcionado no está asociado a ningún scope registrado.');
        }

        return $this->setScope($context['scope'], $context['scope_id']);
    }

    // ======================= Context =========================

    public function setGroup(string $group): static
    {
        $this->context['group'] = $this->validateSlug('group', $group, 16);
        return $this;
    }

    public function setSection(string $section): static
    {
        $this->context['section'] = $this->validateSlug('section', $section, 16);
        return $this;
    }

    public function setSubGroup(string $subGroup): static
    {
        $this->context['sub_group'] = $this->validateSlug('sub_group', $subGroup, 16);
        return $this;
    }

    public function setKeyName(string $keyName): static
    {
        $this->context['key_name'] = $this->validateKeyName($keyName);
        return $this;
    }

    // ======================= Context =========================

    public function asArray(bool $state = true): static
    {
        $this->asArray = $state;
        return $this;
    }

    // ======================= GETTERS =========================

    public function qualifiedKey(?string $key = null): string
    {
        $this->validateContextWithScope();

        return SettingCacheKeyBuilder::build(
            $this->context['namespace'],
            $this->context['environment'],
            $this->context['scope'],
            $this->context['scope_id'],
            $this->context['component'],
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $key ?? $this->context['key_name']
        );
    }

    public function getScopeModel(): ?Model
    {
        return $this->context['scope'] && $this->context['scope_id']
            ? ScopeRegistry::getModelInstance($this->context['scope'], $this->context['scope_id'])
            : null;
    }

    // ======================= BOOLEAN ATTRIBUTES =========================

    public function hasComponentContext(): bool
    {
        return $this->context['namespace']
            && $this->context['environment']
            && $this->context['component'];
    }

    public function hasBaseContext(): bool
    {
        return $this->hasComponentContext()
            && $this->context['key_name'];
    }

    public function hasScopeContext(): bool
    {
        return $this->context['scope']
            && $this->context['scope_id'];
    }

    public function hasGroupContext(): bool
    {
        return $this->context['group']
            && $this->context['section']
            && $this->context['sub_group'];
    }

    public function hasFullContext(): bool
    {
        return $this->hasBaseContext()
            && $this->hasScopeContext()
            && $this->hasGroupContext();
    }

    // ======================= HELPERS =========================

    public function ensureQualifiedKey(): void
    {
        if (!$this->hasBaseContext() || !$this->hasGroupContext()) {
            throw new \InvalidArgumentException("Falta definir el contexto base y 'key_name' en settings().");
        }
    }

    public function resetComponentContext(): void
    {
        $this->context['namespace']   = CoreModule::NAMESPACE;
        $this->context['environment'] = app()->environment();
        $this->context['component']   = CoreModule::COMPONENT;
    }

    public function resetScopeContext(): void
    {
        $this->context['scope']       = null;
        $this->context['scope_id']    = null;
    }

    public function resetGroupContext(): void
    {
        $this->context['group']       = null;
        $this->context['section']     = null;
        $this->context['sub_group']   = null;
    }
}
