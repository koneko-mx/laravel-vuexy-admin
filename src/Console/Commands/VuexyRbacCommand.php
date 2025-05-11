<?php

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleBootManager;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\RBAC\KonekoRbacSyncManager;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vuexy:rbac')]
class VuexyRbacCommand extends Command
{
    protected $signature = 'vuexy:rbac
        {--sync : Importa permisos y roles desde archivos}
        {--export : Exporta permisos y roles a archivos}
        {--publish : Publica los archivos de configuración de roles y permisos}
        {--module= : Especifica un módulo a procesar}
        {--roles-only : Limita la operación solo a roles}
        {--permissions-only : Limita la operación solo a permisos}
        {--overwrite : Sobrescribe roles existentes (solo en sync)}
        {--force : Fuerza publicación aunque ya existan los archivos}';

    protected $description = 'Sincroniza, exporta o publica configuración de RBAC desde archivos JSON por módulo';

    public function handle(): void
    {
        $module  = $this->option('module');
        $sync    = $this->option('sync');
        $export  = $this->option('export');
        $publish = $this->option('publish');

        if (!$sync && !$export && !$publish) {
            $this->error('Debes especificar al menos una acción (--sync, --export o --publish).');
            return;
        }

        $modules = $module
            ? [KonekoModuleRegistry::get($module)]
            : KonekoModuleRegistry::enabled();

        foreach ($modules as $mod) {
            if (!$mod) continue;

            $this->info("\n [🎯 Procesando módulo: {$mod->name}]");

            $basePath = KonekoModuleBootManager::resolvePath($mod, "database/data/rbac");
            File::ensureDirectoryExists($basePath);

            $syncService = new KonekoRbacSyncManager($mod->name, $basePath);

            if ($publish) {
                $syncService->publish($this->option('force'));
                $this->line("📂 Archivos publicados: {$basePath}");
            }

            if ($sync) {
                $syncService->import(
                    onlyPermissions: $this->option('permissions-only'),
                    onlyRoles: $this->option('roles-only'),
                    overwrite: $this->option('overwrite'),
                );
                $this->line("  ✅ Permisos y roles importados desde JSON");
            }

            if ($export) {
                $syncService->export(
                    onlyPermissions: $this->option('permissions-only'),
                    onlyRoles: $this->option('roles-only')
                );
                $this->line("📤 Exportación completada a {$basePath}");
            }
        }

        $this->info("\n 🎉 Operación completada.");
    }
}
