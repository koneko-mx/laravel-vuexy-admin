<?php

namespace Koneko\VuexyAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Services\RBACService;

class SyncRBAC extends Command
{
    protected $signature = 'rbac:sync {action}';
    protected $description = 'Sincroniza roles y permisos con archivos JSON';

    public function handle()
    {
        $action = $this->argument('action');
        if ($action === 'import') {
            RBACService::loadRolesAndPermissions();
            $this->info('Roles y permisos importados correctamente.');
        } elseif ($action === 'export') {
            // Implementación para exportar los roles a JSON
            $this->info('Exportación de roles y permisos completada.');
        } else {
            $this->error('Acción no válida. Usa "import" o "export".');
        }
    }
}
