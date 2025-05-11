<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Macros;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;

App::macro('vuexySettings', function () {
    $settingsService = app(SettingsRepositoryInterface::class);

    return new class($settingsService) {
        /**
         * @var string Default namespace assigned during module boot
         */
        private string $defaultNamespace = '';

        /**
         * @var string|null Current namespace used for operations
         */
        private ?string $currentNamespace = null;

        /**
         * Constructor.
         *
         * @param SettingsRepositoryInterface $settingsService
         */
        public function __construct(private SettingsRepositoryInterface $settingsService) {}

        /**
         * Sets the default namespace (typically from the module at boot time).
         *
         * @param string $namespace
         * @return self
         */
        public function setDefaultNamespace(string $namespace): self
        {
            $this->defaultNamespace = rtrim($namespace, '.') . '.';
            $this->currentNamespace = $this->defaultNamespace;

            return $this;
        }

        /**
         * Sets the namespace to the module's own namespace.
         *
         * @return self
         */
        public function self(): self
        {
            $this->currentNamespace = $this->defaultNamespace;
            return $this;
        }

        /**
         * Switches the namespace to a custom one.
         *
         * @param string $namespace
         * @return self
         */
        public function in(string $namespace): self
        {
            $this->currentNamespace = rtrim($namespace, '.') . '.';
            return $this;
        }

        /**
         * Gets a setting key with the current namespace applied.
         *
         * @param string $key
         * @param mixed ...$args
         * @return mixed
         */
        public function get(string $key, ...$args): mixed
        {
            return $this->settingsService->get($this->qualifyKey($key), ...$args);
        }

        /**
         * Sets a setting key with the current namespace applied.
         *
         * @param string $key
         * @param mixed $value
         * @param mixed ...$args
         * @return mixed
         */
        public function set(string $key, mixed $value, ...$args): mixed
        {
            return $this->settingsService->set($this->qualifyKey($key), $value, ...$args);
        }

        /**
         * Lists all groups (namespaces) available.
         *
         * @return SettingsGroupCollection
         */
        public function listGroups(): SettingsGroupCollection
        {
            // Extraemos todos los keys agrupados
            $allSettings = $this->settingsService->getGroup('');

            // Mapeamos
            $groups = collect($allSettings)
                ->keys()
                ->map(function ($key) {
                    $parts = explode('.', $key);
                    return $parts[0] ?? 'unknown';
                })
                ->unique()
                ->values();

            // Aplicamos filtro si es namespace específico
            if ($this->currentNamespace !== null) {
                $namespaceRoot = rtrim($this->currentNamespace, '.');
                $groups = $groups->filter(fn($group) => $group === $namespaceRoot);
            }

            return new SettingsGroupCollection($groups);
        }

        /**
         * Returns the current namespace.
         *
         * @return string
         */
        public function currentNamespace(): string
        {
            return $this->currentNamespace ?? $this->defaultNamespace;
        }

        /**
         * Internal method to prepend namespace to key.
         *
         * @param string $key
         * @return string
         */
        protected function qualifyKey(string $key): string
        {
            return $this->currentNamespace . $key;
        }
    };
});

/**
 * Small helper class for group collection.
 */
class SettingsGroupCollection extends Collection
{
    /**
     * Adds details (number of keys per group, etc.) to each group.
     *
     * @return Collection
     */
    public function details(): Collection
    {
        return $this->map(function ($group) {
            // Aquí puedes agregar más metadata en el futuro
            return [
                'name' => $group,
                'key_prefix' => $group . '.',
                'total_keys' => settings()->in($group)->countKeys(),
            ];
        });
    }
}
