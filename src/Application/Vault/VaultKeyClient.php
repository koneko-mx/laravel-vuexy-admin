<?php

namespace Koneko\VuexyAdmin\Application\Vault;

use Koneko\VuexyAdmin\Application\Vault\Drivers\VaultKeyApiDriver;
use Koneko\VuexyAdmin\Application\Vault\VaultKeyService;

class VaultKeyClient
{
    protected string $connection;
    protected ?string $project;
    protected ?string $namespace;
    protected string|int|null $clientId;

    public static function make(): static
    {
        return new static();
    }

    public function __construct()
    {
        // Cargar valores por defecto desde settings
        $this->connection = config('settings.security.key_vault.drivers.database.connection', 'vault');
        $this->project    = config('settings.project.code'); // ej. 'erp'
        $this->namespace  = config('settings.security.key_vault.default_namespace', 'default');
        $this->clientId   = config('settings.client.id'); // puede venir de session, JWT, etc.
    }

    public function usingConnection(string $connection): static
    {
        $this->connection = $connection;
        return $this;
    }

    public function fromProject(string $code): static
    {
        $this->project = $code;
        return $this;
    }

    public function withNamespace(string $namespace): static
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function forClient(string|int|null $clientId): static
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Obtiene una clave desencriptada.
     */
    public function get(string $alias): ?string
    {
        $driver = config('settings.security.key_vault.driver', 'laravel');

        return match ($driver) {
            'database'     => $this->getFromDatabase($alias),
            'koneko_api'   => $this->getFromApi($alias),
            'laravel'      => config('app.key'), // fallback interno
            default        => null,
        };
    }

    protected function getFromDatabase(string $alias): ?string
    {
        return VaultKeyService::make()
            ->connection($this->connection)
            ->project($this->project)
            ->namespace($this->namespace)
            ->useClient($this->clientId)
            ->get($alias);
    }

    protected function getFromApi(string $alias): ?string
    {
        return (new VaultKeyApiDriver)
            ->get($this->project, $this->namespace, $alias);
    }


    /**
     * Obtiene el modelo sin desencriptar, por si se requiere metadata.
     */
    public function raw(string $alias)
    {
        return VaultKeyService::make()
            ->connection($this->connection)
            ->project($this->project)
            ->namespace($this->namespace)
            ->useClient($this->clientId)
            ->raw($alias);
    }
}
