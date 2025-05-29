<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Koneko\VuexyAdmin\Application\RBAC\Sync\KonekoRbacSyncManager;

class RbacSeeder extends Seeder
{
    /**
     * Ejecuta el seeder completo para RBAC.
     */
    public function run(): void
    {
        $this->command?->info(" 🔐 Iniciando carga de Roles y Permisos desde módulos...");

        // Sincroniza permisos y roles de todos los módulos
        KonekoRbacSyncManager::importAll();

        $this->command?->info(" Roles y permisos cargados exitosamente.");
    }
}
