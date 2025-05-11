<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Application\System\RbacManagerService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vuexy:rbac')]
class __VuexyRbacCommand extends Command
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

    protected $description = '🔐 Sincroniza, exporta o publica los roles y permisos RBAC de Vuexy por módulo';

    public function handle()
    {
        $moduleName   = $this->option('module');
        $sync         = $this->option('sync');
        $export       = $this->option('export');
        $publish      = $this->option('publish');
        $rolesOnly    = $this->option('roles-only');
        $permissionsOnly = $this->option('permissions-only');
        $overwrite    = $this->option('overwrite');
        $force        = $this->option('force');

        if (!($sync || $export || $publish)) {
            $this->error('Debes especificar al menos una opción: --sync, --export o --publish');
            return 1;
        }

        $modules = $moduleName
            ? [KonekoModuleRegistry::get($moduleName)]
            : KonekoModuleRegistry::enabled();

        foreach ($modules as $module) {
            if (!$module) {
                $this->warn("⚠️ Módulo no encontrado: {$moduleName}");
                continue;
            }

            $this->info("🎯 Procesando módulo: {$module->name}");

            if ($sync) {
                if (!$rolesOnly) {
                    $this->line("  📥 Importando permisos...");
                    RbacManagerService::importPermissions($module);
                }

                if (!$permissionsOnly) {
                    $this->line("  🔐 Importando roles...");
                    RbacManagerService::importRoles($module, $overwrite);
                }
            }

            if ($export) {
                if (!$rolesOnly) {
                    $this->line("  📤 Exportando permisos...");
                    RbacManagerService::exportPermissions($module);
                }

                if (!$permissionsOnly) {
                    $this->line("  🔐 Exportando roles...");
                    RbacManagerService::exportRoles($module);
                }
            }

            if ($publish) {
                // Puedes definir aquí lógica futura si decides generar archivos
                // en `base_path('database/data/vuexy-x/rbac.json')` en lugar de sobrescribir los originales
                $this->comment("🚧 Publish: aún no implementado. Usa export + config para lograrlo.");
            }
        }

        $this->info('✅ Comando RBAC finalizado.');
        return 0;
    }
}
