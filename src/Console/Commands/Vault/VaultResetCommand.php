<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands\Vault;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vault:reset', description: 'Elimina la tabla de claves del vault si el entorno lo permite.')]
class VaultResetCommand extends Command
{
    protected $signature = 'vault:reset {--force : Ejecutar sin confirmación}';
    //protected $description = 'Elimina la tabla de claves del vault si el entorno lo permite.';

    public function handle(): void
    {
        // Seguridad: Confirmar que está habilitado
        if (!Config::get('settings.security.key_vault.reset_enabled', false)) {
            $this->warn('❌ Vault reset está deshabilitado por configuración. Revisa KONEKO_KEY_VAULT_RESET_ENABLED.');
            return;
        }

        // Confirmación interactiva
        if (!$this->option('force') && !$this->confirm('¿Seguro que deseas eliminar todas las claves del vault?')) {
            $this->info('⛔ Operación cancelada.');
            return;
        }

        $connection = Config::get('settings.security.key_vault.drivers.database.connection', 'vault');
        $table      = Config::get('settings.security.key_vault.drivers.database.table', 'vault_keys');

        if (!Schema::connection($connection)->hasTable($table)) {
            $this->warn("⚠️ La tabla `{$table}` no existe en la conexión [{$connection}].");
            return;
        }

        Schema::connection($connection)->dropIfExists($table);
        $this->info("✅ Tabla `{$table}` eliminada correctamente de la conexión [{$connection}].");
    }
}
