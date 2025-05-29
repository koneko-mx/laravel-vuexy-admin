<?php

namespace Koneko\VuexyAdmin\Console\Commands\Vault;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Vault\VaultKeyService;

class VaultKeyRotateCommand extends Command
{
    protected $signature = 'vault:rotate {alias : Alias de la clave a rotar}
                                        {--project= : Código del proyecto}
                                        {--namespace= : Namespace (opcional)}
                                        {--client= : ID de cliente (UUID o int)}
                                        {--length=32 : Longitud de la nueva clave}';

    protected $description = '🔄 Rota una clave del vault generando un nuevo valor aleatorio.';

    public function handle(): void
    {
        $alias     = $this->argument('alias');
        $project   = $this->option('project') ?? config('settings.project.code');
        $namespace = $this->option('namespace') ?? 'default';
        $clientId  = $this->option('client') ?? null;
        $length    = (int) $this->option('length');

        $newKey = VaultKeyService::generateRandomKey($length);

        $success = VaultKeyService::make()
            ->project($project)
            ->namespace($namespace)
            ->useClient($clientId)
            ->rotate($alias, $newKey);

        if ($success) {
            $this->info("✅ Clave '{$alias}' rotada exitosamente.");
        } else {
            $this->error("❌ No se encontró la clave '{$alias}'.");
        }
    }
}
