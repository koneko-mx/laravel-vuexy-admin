<?php

namespace Koneko\VuexyAdmin\Application\Config\Manager;

use Illuminate\Support\Facades\{Auth, Config};
use Koneko\VuexyAdmin\Application\Config\Registry\ConfigBlockRegistry;
use Koneko\VuexyAdmin\Application\Config\Contracts\ConfigRepositoryInterface;
use Koneko\VuexyAdmin\Application\CoreModule;
use Koneko\VuexyAdmin\Application\Traits\System\Context\{HasBaseContext, HasConfigContextValidation};

final class KonekoConfigManager implements ConfigRepositoryInterface
{
    use HasBaseContext;
    use HasConfigContextValidation;

    public bool $fromDb = false;

    public function __construct()
    {
        $this->setNamespace()
            ->environment()
            ->loadModuleClass(CoreModule::class);
    }

    // ==================== Factory ====================

    public static function make(): static
    {
        return new static();
    }

    // ======================= 🔍 LECTURA =========================

    public function get(?string $keyName = null, mixed $default = null): mixed
    {
        $this->keyName($keyName ?? $this->context['key_name']);

        // Resolvemos el qualified key
        $qualifiedKey = $this->getQualifiedKey();

        // Si directo, obtenemos el valor directamente de config
        if (!$this->fromDb) {
            return config($qualifiedKey, $default);
        }

        // Prioridad 1: override desde settings
        $value = settings()
            ->setContextArray($this->context)
            ->get($this->context['key_name']);

        // Prioridad 2: valor directo de config
        return $value ?? config($qualifiedKey, $default);
    }

    public function fromDb(bool $fromDb = true): static
    {
        $this->fromDb = $fromDb;
        return $this;
    }

    public function has(string $key): bool
    {
        $this->keyName($key);
        return settings()->setContextArray($this->context)->exists($key)
            || config()->has($this->getQualifiedKey());
    }

    public function sourceOf(?string $key = null): string
    {
        $this->keyName($key ?? $this->context['key_name']);
        $qualifiedKey = $this->getQualifiedKey();

        // Prioridad 1: override desde settings
        if (settings()->setContextArray($this->context)->exists($this->context['key_name'])) {
            return 'database';
        }

        // Prioridad 2: valor directo de config
        if (config()->has($qualifiedKey)) {
            return 'config';
        }

        return 'default';
    }

    public function info(): array
    {
        $qualified = $this->getQualifiedKey();

        return [
            'qualified_key' => $qualified,
            'context'       => $this->context,
            'value'         => $this->get(),
            'source'        => $this->sourceOf(),
            'has_config'    => config()->has($qualified),
            'has_db'        => settings()->setContextArray($this->context)->exists($this->context['key_name']),
        ];
    }

    // ======================= HELPERS =========================

    public function context(?string $group, ?string $section = null, ?string $subGroup = null): static
    {
        $this->context['group']     = $group ? $this->validateSlug('group', $group, 16) : null;
        $this->context['section']   = $section ? $this->validateSlug('section', $section, 16) : null;
        $this->context['sub_group'] = $subGroup ? $this->validateSlug('sub_group', $subGroup, 16) : null;

        return $this;
    }

    protected function validateKeyName(string $keyName): string
    {
        if (!preg_match('/^[a-zA-Z0-9-._]+$/', $keyName)) {
            throw new \InvalidArgumentException("El valor '{$keyName}' de 'keyName' debe ser un string válido.");
        }

        if (strlen($keyName) > 64) {
            throw new \InvalidArgumentException("El valor de 'keyName' excede 64 caracteres.");
        }

        return $keyName;
    }

    public function ensureQualifiedKey(): void
    {
        if (!$this->hasBaseContext()) {
            throw new \InvalidArgumentException("Falta definir el contexto base y 'key_name' en config().");
        }
    }

    // ======================= GETTERS =========================

    public function getQualifiedKey(?string $key = null): string
    {
        $parts = [
            $this->context['namespace'],
            $this->context['component'],
            $this->context['group'],
            $this->context['section'],
            $this->context['sub_group'],
            $key ?? $this->context['key_name'],
        ];

        return collect($parts)
            ->filter()
            ->implode('.');
    }

    public function qualifiedKeyPrefix(): string
    {
        $parts = [
            $this->context['namespace'],
            $this->context['component'],
        ];

        return collect($parts)->filter()->implode('.');
    }

    // ======================= HELPERS =========================

    public function reset(): static
    {


        return $this;
    }

    // ======================= Config Blocks =========================

    public function syncFromRegistry(string $configKey, bool $forceReload = false): static
    {
        $config = ConfigBlockRegistry::get($configKey);

        $manager = cache_m()
            ->component($config['component'])
            ->context($config['group'], $config['section'], $config['sub_group'])
            ->user(Auth::user())
            ->keyName($config['key_name']);

        if ($forceReload) {
            $manager->forget();
        }

        $castFn = isset($config['cast']) && class_exists($config['cast'])
            ? [app($config['cast']), 'cast']
            : fn ($v, $k) => $v;

        if (!$manager->isEnabled()) {
            // Bypass de cache: usamos el callback sin guardar en Redis
            $castFn = isset($config['cast']) && class_exists($config['cast'])
                ? [app($config['cast']), 'cast']
                : fn ($v, $k) => $v;

            $base     = config($configKey, []);
            $settings = settings()
                ->component($config['component'])
                ->context($config['group'], $config['section'], $config['sub_group'])
                ->user(Auth::user())
                ->getSubGroup(true);

            $merged = array_replace_recursive($base, array_map($castFn, $settings, array_keys($settings)));

        } else {
            // Cache activada, usamos remember
            $merged = $manager->rememberWithTTLResolution(function () use ($configKey, $config, $castFn) {
                $base     = config($configKey, []);
                $settings = settings()
                    ->component($config['component'])
                    ->context($config['group'], $config['section'], $config['sub_group'])
                    ->user(Auth::user())
                    ->getSubGroup(true);

                return array_replace_recursive($base, array_map($castFn, $settings, array_keys($settings)));
            });
        }

        Config::set($configKey, $merged);

        return $this;
    }

    // ======================= ESCRITURA =========================

    public function set(mixed $value, ?string $keyName = null): void
    {
        $this->keyName($keyName ?? $this->context['key_name']);
        $qualified = $this->getQualifiedKey();

        // Seguridad: solo sobrescribir valores existentes en config
        if (!config()->has($qualified)) {
            throw new \LogicException("❌ No se puede sobrescribir '{$qualified}' porque no existe en archivo de configuración.");
        }

        // Seguridad: si ya hay un setting y no es de tipo config
        $existing = settings()
            ->setContextArray($this->context)
            ->get($this->context['key_name']);

        if ($existing && !($existing->is_config ?? false)) {
            throw new \LogicException("⚠️ El setting '{$qualified}' ya existe en DB pero no está marcado como 'is_config'.");
        }

        // Escritura segura con flag `is_config = true`
        settings()
            ->setContextArray($this->context)
            ->markAsSystem(true)
            ->markAsActive(true)
            ->setDescription("Override del archivo de configuración '{$qualified}'")
            ->setHint("Este valor reemplaza el valor original definido en config/")
            ->setInternalConfigFlag()
            ->set($value);
    }
}
