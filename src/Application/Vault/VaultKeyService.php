<?php

namespace Koneko\VuexyAdmin\Application\Vault;

use Koneko\VuexyAdmin\Models\VaultClientKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class VaultKeyService
{
    protected string $connection = 'vault';
    protected ?string $projectCode = null;
    protected ?string $namespace = null;
    protected string|int|null $clientId = null;

    public static function make(): static
    {
        return new static();
    }

    public static function fromConfig(): static
    {
        return static::make()
            ->connection(config('settings.security.key_vault.drivers.database.connection', 'vault'))
            ->project(config('settings.security.key_vault.default_project'))
            ->namespace(config('settings.security.key_vault.default_namespace'))
            ->useClient(config('settings.security.key_vault.default_client_id'));
    }


    public function connection(string $name): static
    {
        $this->connection = $name;
        return $this;
    }

    public function project(string $code): static
    {
        $this->projectCode = $code;
        return $this;
    }

    public function namespace(string $namespace): static
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function useClient(string|int|null $clientId): static
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function get(string $alias): ?string
    {
        $model = $this->query()->byAlias($alias)->first();

        if (!$model) {
            return null;
        }

        return $model->key_material
            ? Crypt::decryptString($model->key_material)
            : null;
    }

    public function raw(string $alias): ?VaultClientKey
    {
        return $this->query()->byAlias($alias)->first();
    }

    public function rotate(string $alias, string $newMaterial): bool
    {
        $model = $this->raw($alias);

        if (!$model) {
            return false;
        }

        $model->key_material = Crypt::encryptString($newMaterial);
        $model->rotated_at = now();
        $model->rotation_count++;
        $model->save();

        return true;
    }

    public function query()
    {
        return VaultClientKey::on($this->connection)
            ->newQuery()
            ->active()
            ->when($this->projectCode, fn($q) => $q->byProject($this->projectCode))
            ->when($this->namespace, fn($q) => $q->withNamespace($this->namespace))
            ->when($this->clientId, fn($q) => $q->where('client_id', $this->clientId));
    }
}
