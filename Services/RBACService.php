<?php

namespace Koneko\VuexyAdmin\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class RBACService
{
    public static function loadRolesAndPermissions()
    {
        $filePath = database_path('data/rbac-config.json');
        if (!File::exists($filePath)) {
            throw new \Exception("Archivo de configuración RBAC no encontrado.");
        }

        $rbacData = json_decode(File::get($filePath), true);
        foreach ($rbacData['permissions'] as $perm) {
            Permission::updateOrCreate(['name' => $perm]);
        }

        foreach ($rbacData['roles'] as $name => $role) {
            $roleInstance = Role::updateOrCreate(['name' => $name, 'style' => $role['style']]);
            $roleInstance->syncPermissions($role['permissions']);
        }
    }
}
