<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Config\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ConfigRepositoryInterface
{
    // ==================== Factory ====================
    public static function make(array $context = []): static;


    // ==================== Context ====================
    public function namespace(?string $namespace): static;
    public function environment(?string $env = null): static;
    public function component(string $component): static;
    public function context(?string $group, ?string $section, ?string $subGroup = 'default'): static;
    public function ctx(string $path): static; // "group.section.sub"
    public function scope(Model|string|null $scope, ?int $id = null): static;
    public function scopeId(?int $scopeId): static;

    public function group(string $group): static;
    public function section(string $section): static;
    public function subGroup(string $subGroup): static;
    public function keyName(string $keyName): static;


    // ==================== Lectura ====================
    /**
     * Lee una clave de configuración:
     * - Si fromDb(false): solo archivos config()/env()
     * - Si fromDb(true): intenta overlay en Settings (DB) y cae a config()
     */
    public function get(?string $keyName = null, mixed $default = null): mixed;

    /** Habilita overlay desde Settings (DB) */
    public function fromDb(bool $fromDb = true): static;

    /**
     * Origen del valor: 'database' | 'config' | 'default'
     * keyName es opcional; usa el del contexto si no se pasa.
     */
    public function sourceOf(?string $keyName = null): string;


    // ==================== Claves calif. / Scope ====================
    public function getQualifiedKey(?string $keyName = null): string;
    public function qualifiedKeyPrefix(): string;
    public function getScopeModel(): ?Model;


    // ==================== Utils ====================
    /** ¿Existe un valor para ese keyName en DB (overlay) o en archivo de config? */
    public function hasKeyName(?string $keyName = null): bool;

    /** ¿Existe en archivo de config o DB, dado un qualified key explícito? */
    public function hasQualifiedKey(?string $qualifiedKey = null): bool;


    // ==================== Diagnostics ====================
    public function info(): array;
}
