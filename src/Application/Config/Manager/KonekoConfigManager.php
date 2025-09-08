<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Config\Manager;

use Illuminate\Support\Facades\Config;
use Koneko\VuexyAdmin\Application\Config\Contracts\ConfigRepositoryInterface;
use Koneko\VuexyAdmin\Application\Config\Registry\ConfigBlockRegistry;
use Koneko\VuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\VuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\VuexyAdmin\Application\Cache\Builders\SettingCacheKeyBuilder;
use Koneko\VuexyAdmin\Application\Traits\System\Context\{HasBaseContext, HasConfigContextValidation};

final class KonekoConfigManager implements ConfigRepositoryInterface
{
    use HasBaseContext;
    use HasConfigContextValidation;

    /** Si true, intenta overlay desde Settings (DB) antes de caer a config() */
    private bool $fromDb = false;

    public function __construct()
    {
        $namespace = config('koneko.namespace', 'koneko');
        $this->namespace($namespace)->environment();
    }

    // ==================== Factory ====================

    public static function make(array $context = []): static
    {
        $i = new static();
        if ($context) {
            $i->setContextArray($context);
        }
        return $i;
    }

    // ==================== Lectura ====================

    public function get(?string $keyName = null, mixed $default = null): mixed
    {
        if ($keyName) {
            $this->keyName($keyName);
        }

        $qualifiedConfigKey = $this->getQualifiedKey();

        if (!$this->fromDb) {
            return Config::get($qualifiedConfigKey, $default);
        }

        // Overlay: intenta Settings (DB) con el contexto actual
        $dbValue = $this->readFromDb($this->context['key_name'] ?? null);
        return ($dbValue !== null) ? $dbValue : Config::get($qualifiedConfigKey, $default);
    }

    public function fromDb(bool $fromDb = true): static
    {
        $this->fromDb = $fromDb;
        return $this;
    }

    public function sourceOf(?string $keyName = null): string
    {
        if ($keyName) {
            $this->keyName($keyName);
        }

        $qualifiedConfigKey = $this->getQualifiedKey();

        // 1) DB overlay
        if ($this->dbHasKey($this->context['key_name'] ?? null)) {
            return 'database';
        }

        // 2) Archivo config
        if (Config::has($qualifiedConfigKey)) {
            return 'config';
        }

        return 'default';
    }

    // ==================== Claves calificadas / Scope ====================

    public function getQualifiedKey(?string $keyName = null): string
    {
        $keyName = $keyName ?? ($this->context['key_name'] ?? null);
        /*
        if (!$keyName) {
            throw new \InvalidArgumentException("Falta 'key_name' para construir la clave calificada de configuración.");
        }
        */

        // No incluye environment ni scope (esto es un key de archivo de config)
        $parts = [
            $this->context['namespace'] ?? null,
            $this->context['component'] ?? null,
            $this->context['group']     ?? null,
            $this->context['section']   ?? null,
            $this->context['sub_group'] ?? null,
            $keyName,
        ];

        return collect($parts)->filter()->implode('.');
    }

    public function qualifiedKeyPrefix(): string
    {
        return collect([
            $this->context['namespace'],
            $this->context['component'],
        ])->filter()->implode('.');
    }

    public function getScopeModel(): ?\Illuminate\Database\Eloquent\Model
    {
        // delega al trait
        return ($this->context['scope'] && $this->context['scope_id'])
            ? \Koneko\VuexyAdmin\Application\Settings\Registry\ScopeRegistry::getModelInstance(
                $this->context['scope'],
                $this->context['scope_id']
            )
            : null;
    }

    // ==================== Utils ====================

    public function hasKeyName(?string $keyName = null): bool
    {
        $keyName = $keyName ?? ($this->context['key_name'] ?? null);
        if (!$keyName) {
            return false;
        }

        $configKey = $this->getQualifiedKey($keyName);
        if (Config::has($configKey)) {
            return true;
        }

        // Si falta group/section/sub_group no podemos derivar la clave de DB
        if (empty($this->context['group']) || empty($this->context['section']) || empty($this->context['sub_group'])) {
            return false;
        }

        return $this->dbHasKey($keyName);
    }

    public function hasQualifiedKey(?string $qualifiedKey = null): bool
    {
        $qualifiedKey ??= $this->getQualifiedKey();

        // Config (archivo)
        if (Config::has($qualifiedKey)) {
            return true;
        }

        // DB overlay: derivar group/section/sub_group/key_name desde el qualified de config
        $parsed = $this->parseQualifiedConfigKey($qualifiedKey);
        if (!$parsed['key_name']) {
            return false;
        }

        // Si el contexto actual no define environment/scope, igual intentamos con los actuales (o defaults de trait)
        $dbKey = $this->buildDbQualifiedKey(
            $parsed['group'],
            $parsed['section'],
            $parsed['sub_group'],
            $parsed['key_name']
        );

        // Si no se puede construir, no hay DB overlay para esta combinación
        if ($dbKey === null) {
            return false;
        }

        return KonekoSettingManager::make()->hasQualifiedKey($dbKey);
    }

    public function info(): array
    {
        $qualified = $this->getQualifiedKey();

        return [
            'qualified_key' => $qualified,
            'context'       => $this->context,
            'from_db'       => $this->fromDb,
            'source'        => $this->sourceOf(),
            'has_config'    => Config::has($qualified),
            'has_db'        => $this->dbHasKey($this->context['key_name'] ?? null),
            'value'         => $this->get(),
        ];
    }

    // ==================== Extra (opcional): sincronizar bloques ====================

    /**
     * Sincroniza/compone un bloque de configuración registrado en ConfigBlockRegistry
     * mezclando el archivo base con los valores de Settings (por sub_group),
     * y cacheando el resultado si la caché está habilitada.
     *
     * NO forma parte del contrato, pero es muy útil en DX.
     */
    public function syncFromRegistry(string $configKey, bool $forceReload = false): static
    {
        $block = ConfigBlockRegistry::get($configKey);

        // Normaliza defaults
        $group     = $block['group']     ?? null;
        $section   = $block['section']   ?? 'default';
        $subGroup  = $block['sub_group'] ?? 'default';
        $keyName   = $block['key_name']  ?? 'config';
        $component = $block['component'] ?? ($this->context['component'] ?? 'app');
        $ttl       = $block['ttl']       ?? null;

        // Cast: class-string con método cast($value, $key) o callable($value, $key)
        $castFn = $this->resolveCastCallable($block['cast'] ?? null);

        // Contexto completo para cache/settings
        $ctx = array_filter([
            'namespace'   => $this->context['namespace']   ?? config('koneko.namespace', 'koneko'),
            'environment' => $this->context['environment'] ?? app()->environment(),
            'component'   => $component,
            'group'       => $group,
            'section'     => $section,
            'sub_group'   => $subGroup,
            'key_name'    => $keyName,
            // Scope opcional definido por el bloque
            'scope'       => $block['scope']    ?? ($this->context['scope']    ?? null),
            'scope_id'    => $block['scope_id'] ?? ($this->context['scope_id'] ?? null),
        ], static fn($v) => $v !== null);

        // Manager de caché para componer el bloque (clave única por bloque)
        $cache = KonekoCacheManager::make($ctx);

        if ($forceReload) {
            $cache->forget();
        }

        $compose = function () use ($configKey, $ctx, $castFn): array {
            $base     = Config::get($configKey, []);
            $settings = KonekoSettingManager::make($ctx)->asArray(true)->all(); // key_name => value

            // Aplicar cast por clave
            $casted = [];
            foreach ($settings as $k => $v) {
                $casted[$k] = $castFn ? $castFn($v, $k) : $v;
            }

            // Mezcla determinista (DB sobrescribe archivo)
            return array_replace_recursive($base, $casted);
        };

        $merged = $cache->remember($compose, $ttl);

        // Publica en runtime
        Config::set($configKey, $merged);

        return $this;
    }

    // ==================== Internos ====================

    private function readFromDb(?string $keyName): mixed
    {
        if (!$keyName) {
            return null;
        }

        // Requiere group/section/sub_group para mapear a DB
        if (empty($this->context['group']) || empty($this->context['section']) || empty($this->context['sub_group'])) {
            return null;
        }

        $dbKey = $this->buildDbQualifiedKey(
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $keyName
        );

        if ($dbKey === null) {
            return null;
        }

        // No necesitamos tocar caché aquí, simplemente leer del manager de settings
        return KonekoSettingManager::make($this->context)->get($keyName);
    }

    private function dbHasKey(?string $keyName): bool
    {
        if (!$keyName) {
            return false;
        }

        if (empty($this->context['group']) || empty($this->context['section']) || empty($this->context['sub_group'])) {
            return false;
        }

        $dbKey = $this->buildDbQualifiedKey(
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $keyName
        );

        if ($dbKey === null) {
            return false;
        }

        return KonekoSettingManager::make()->hasQualifiedKey($dbKey);
    }

    /**
     * Construye la clave completa de DB (namespace.env.scope:scopeId.component.group.section.sub_group.key_name)
     * a partir del contexto actual y los segmentos lógicos del key de config.
     */
    private function buildDbQualifiedKey(?string $group, ?string $section, ?string $subGroup, ?string $keyName): ?string
    {
        if (!$group || !$section || !$subGroup || !$keyName) {
            return null;
        }

        return SettingCacheKeyBuilder::build(
            $this->context['namespace']   ?? config('koneko.namespace', 'koneko'),
            $this->context['environment'] ?? app()->environment(),
            $this->context['scope']       ?? null,
            $this->context['scope_id']    ?? null,
            $this->context['component']   ?? 'app',
            $group,
            $section,
            $subGroup,
            $keyName
        );
    }

    /**
     * Parsea un qualified key de config en segmentos.
     * Formato esperado: namespace.component[.group[.section[.sub_group]]].key_name
     */
    private function parseQualifiedConfigKey(string $qualified): array
    {
        $parts = explode('.', $qualified);
        $count = count($parts);

        if ($count < 3) {
            return [
                'namespace' => null, 'component' => null,
                'group' => null, 'section' => null, 'sub_group' => null,
                'key_name' => null,
            ];
        }

        $namespace = $parts[0] ?? null;
        $component = $parts[1] ?? null;

        // Resto: group.section.sub_group.key_name (algunos opcionales)
        $rest = array_slice($parts, 2);

        $keyName  = array_pop($rest) ?? null;
        $group    = $rest[0] ?? null;
        $section  = $rest[1] ?? null;
        $subGroup = $rest[2] ?? null;

        return compact('namespace', 'component', 'group', 'section', 'sub_group', 'key_name');
    }

    /**
     * Normaliza un "caster": class-string con método cast($value, $key) o callable($value, $key).
     */
    private function resolveCastCallable(mixed $cast): ?callable
    {
        if (!$cast) {
            return null;
        }

        if (is_callable($cast)) {
            return $cast(...);
        }

        if (is_string($cast) && class_exists($cast)) {
            $instance = app($cast);
            if (method_exists($instance, 'cast')) {
                return [$instance, 'cast'];
            }
        }

        // Fallback: ignora caster inválido
        return null;
    }

    protected function validateKeyName(string $keyName): string
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $keyName)) {
            throw new \InvalidArgumentException(
                "El valor '{$keyName}' de 'keyName' debe ser alfanumérico con '.', '_' o '-'."
            );
        }
        if (strlen($keyName) > 64) {
            throw new \InvalidArgumentException("El valor de 'keyName' excede 64 caracteres.");
        }
        return $keyName;
    }

    protected function validateSlug(string $field, string $value, int $maxLength): string
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $value)) {
            throw new \InvalidArgumentException("El valor '{$value}' de '{$field}' debe ser alfanumérico con '.', '_' o '-'.");
        }

        if (strlen($value) > $maxLength) {
            throw new \InvalidArgumentException("El valor de '{$field}' excede {$maxLength} caracteres.");
        }

        return $value;
    }

}
