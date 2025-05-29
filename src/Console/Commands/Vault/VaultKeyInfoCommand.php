<?php

namespace Koneko\VuexyAdmin\Console\Commands\Vault;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Vault\VaultKeyService;

class VaultKeyInfoCommand extends Command
{
    protected $signature = 'vault:info {alias : Alias de la clave}
                                     {--project= : Código del proyecto}
                                     {--namespace= : Namespace (opcional)}
                                     {--client= : ID de cliente (UUID o int)}';

    protected $description = '📌 Muestra información detallada de una clave del vault.';

    public function handle(): void
    {
        $alias     = $this->argument('alias');
        $project   = $this->option('project') ?? config('settings.project.code');
        $namespace = $this->option('namespace') ?? 'default';
        $clientId  = $this->option('client') ?? null;

        $vault = VaultKeyService::make()
            ->project($project)
            ->namespace($namespace)
            ->useClient($clientId);

        $key = $vault->raw($alias);

        if (!$key) {
            $this->error("❌ Clave '{$alias}' no encontrada.");
            return;
        }

        $this->info("🔐 Información de clave:");
        $this->line("Alias:        {$key->alias}");
        $this->line("Proyecto:     {$key->project_code}");
        $this->line("Cliente ID:   {$key->client_id}");
        $this->line("Namespace:    {$key->namespace}");
        $this->line("Activo:       " . ($key->is_active ? 'Sí' : 'No'));
        $this->line("Algoritmo:    {$key->algorithm}");
        $this->line("Sensitiva:    " . ($key->is_sensitive ? 'Sí' : 'No'));
        $this->line("Rotaciones:   {$key->rotation_count}");
        $this->line("Última rotación: " . ($key->rotated_at ?? 'Nunca'));
        $this->line("Creada en:    {$key->created_at}");
    }
}
