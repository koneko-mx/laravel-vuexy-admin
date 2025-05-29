<?php

namespace Koneko\VuexyAdmin\Console\Commands\Vault;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Vault\VaultKeyService;

class VaultKeyCommand extends Command
{
    protected $signature = 'vault:key
        {action : generate|rotate|deactivate|list}
        {alias? : Alias de la clave}
        {--owner=default_project : Nombre del proyecto propietario}
        {--algorithm=AES-256-CBC : Algoritmo de encriptación}
        {--sensitive=1 : Marcar clave como sensible (1 o 0)}';

    protected $description = 'Gestión de claves seguras en el Key Vault';

    public function handle(): void
    {
        $service = app(VaultKeyService::class);
        $action = strtolower($this->argument('action'));
        $alias = $this->argument('alias');
        $owner = $this->option('owner');
        $algorithm = $this->option('algorithm');
        $isSensitive = (bool) $this->option('sensitive');

        match ($action) {
            'generate'   => $this->generateKey($service, $alias, $owner, $algorithm, $isSensitive),
            'rotate'     => $this->rotateKey($service, $alias),
            'deactivate' => $this->deactivateKey($service, $alias),
            'list'       => $this->listKeys($service),
            default      => $this->error("Acción '{$action}' no válida. Usa: generate, rotate, deactivate, list."),
        };
    }

    protected function generateKey(VaultKeyService $service, ?string $alias, string $owner, string $algorithm, bool $isSensitive): void
    {
        if (!$alias) {
            $this->error('El alias es obligatorio para generar una clave.');
            return;
        }

        $key = $service->generateKey($alias, $owner, $algorithm, $isSensitive);
        $this->info("✅ Clave '{$alias}' generada correctamente.");
    }

    protected function rotateKey(VaultKeyService $service, ?string $alias): void
    {
        if (!$alias) {
            $this->error('El alias es obligatorio para rotar una clave.');
            return;
        }

        $service->rotateKey($alias);
        $this->info("🔄 Clave '{$alias}' rotada correctamente.");
    }

    protected function deactivateKey(VaultKeyService $service, ?string $alias): void
    {
        if (!$alias) {
            $this->error('El alias es obligatorio para desactivar una clave.');
            return;
        }

        $service->deactivateKey($alias);
        $this->info("🚫 Clave '{$alias}' desactivada.");
    }

    protected function listKeys(VaultKeyService $service): void
    {
        $keys = VaultClientKey::all(['alias', 'owner_project', 'algorithm', 'is_active', 'rotated_at']);

        if ($keys->isEmpty()) {
            $this->info('📭 No hay claves registradas.');
            return;
        }

        $this->table(['Alias', 'Proyecto', 'Algoritmo', 'Activa', 'Rotada en'], $keys->toArray());
    }
}
