<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Flags;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Koneko\VuexyAdmin\Application\Bootstrap\Extenders\Model\ModelExtensionRegistry;

trait HasFlags
{
    protected static array $registeredFlags = [];

    public static function registerFlag(string $flag, string $description): void
    {
        static::$registeredFlags[static::class][$flag] = $description;

        // Crear método dinámico si no existe
        $methodName = 'is' . str_replace('_', '', ucwords($flag, '_'));
        if (!method_exists(static::class, $methodName)) {
            static::macro($methodName, fn() => $this->hasFlag($flag));
        }
    }

    public static function getRegisteredFlags(): array
    {
        return static::$registeredFlags[static::class] ?? [];
    }


    /**
     * Boot the trait
     */
    protected static function bootHasFlags(): void
    {
        static::retrieved(function ($model) {
            $model->flags = $model->flags ?? [];
        });

        // Registrar flags desde el registro global
        $flags = ModelExtensionRegistry::getFlagsFor(static::class);

        foreach ($flags as $flag => $desc) {
            static::registerFlag($flag, $desc);
        }
    }

    /**
     * Set a flag value
     */
    public function setFlag(string|object $flag, bool $value = true): self
    {
        $flagName = $this->resolveFlagName($flag);

        $this->flags = array_merge($this->flags ?? [], [$flagName => $value]);

        return $this;
    }

    /**
     * Check if a flag is set
     */
    public function hasFlag(string|object $flag): bool
    {
        $flagName = $this->resolveFlagName($flag);

        return (bool) ($this->flags[$flagName] ?? false);
    }

    /**
     * Remove a flag
     */
    public function removeFlag(string|object $flag): self
    {
        $flagName = $this->resolveFlagName($flag);

        if (isset($this->flags[$flagName])) {
            unset($this->flags[$flagName]);
        }

        return $this;
    }

    /**
     * Toggle a flag value
     */
    public function toggleFlag(string|object $flag): self
    {
        $flagName = $this->resolveFlagName($flag);

        return $this->setFlag($flagName, !$this->hasFlag($flagName));
    }

    /**
     * Get all active flags
     */
    public function activeFlags(): array
    {
        return array_filter($this->flags ?? [], fn ($value) => $value);
    }

    /**
     * Scope to filter by flag
     */
    public function scopeWithFlag(Builder $query, string|object $flag, bool $value = true): Builder
    {
        $flagName = $this->resolveFlagName($flag);

        return $query->where("flags->{$flagName}", $value);
    }

    /**
     * Resolve flag name (supports Enums)
     */
    protected function resolveFlagName(string|object $flag): string
    {
        if (is_object($flag)) {
            if (!method_exists($flag, 'value')) {
                throw new InvalidArgumentException("El flag debe ser un string o un Enum con método value()");
            }

            return $flag->value;
        }

        return $flag;
    }

    /**
     * Dynamic method calls (e.g., $user->isAdmin())
     */
    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'is') && str_ends_with($method, 'Flag')) {
            $flag = strtolower(substr($method, 2, -4));

            return $this->hasFlag($flag);
        }

        if (str_starts_with($method, 'is')) {
            $flag = strtolower(substr($method, 2));

            return $this->hasFlag($flag);
        }

        return parent::__call($method, $parameters);
    }
}
