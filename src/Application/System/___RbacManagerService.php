<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\{File,Config};
use Spatie\Permission\Models\{Permission, Role};
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;

class ___RbacManagerService
{
    public static function importAll(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            static::importPermissions($module);

            $moduleKey = $module->slug;

            $moduleConfig = Config::get("vuexy-admin.rbac.modules.{$moduleKey}", []);

            if (($moduleConfig['publish_roles'] ?? false) || ($moduleConfig['auto_overwrite_roles'] ?? false)) {
                static::importRoles($module, (bool) ($moduleConfig['auto_overwrite_roles'] ?? false));
            }
        }
    }

    public static function importPermissions($module): void
    {
        $path = $module->rbac['permissions_path'] ?? null;

        if (!$path || !file_exists($module->basePath . '/' . $path)) {
            return;
        }

        $json = json_decode(file_get_contents($module->basePath . '/' . $path), true);

        foreach ($json as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }

    public static function importRoles($module, bool $overwrite = false): void
    {
        $path = $module->rbac['roles_path'] ?? null;

        if (!$path || !file_exists($module->basePath . '/' . $path)) {
            return;
        }

        $json = json_decode(file_get_contents($module->basePath . '/' . $path), true);

        foreach ($json as $name => $data) {
            $role = Role::firstOrCreate(['name' => $name], ['style' => $data['style'] ?? 'primary']);

            if ($overwrite) {
                $role->syncPermissions($data['permissions'] ?? []);

            } else {
                $role->givePermissionTo($data['permissions'] ?? []);
            }
        }
    }

    public static function exportAll(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            static::exportPermissions($module);
            static::exportRoles($module);
        }
    }

    public static function exportPermissions($module): void
    {
        $path = $module->rbac['permissions_path'] ?? null;

        if (!$path) return;

        $permissions = Permission::query()
            ->where('name', 'LIKE', "%{$module->name}%")
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();

        static::writeJson($module->basePath . '/' . $path, $permissions);
    }

    public static function exportRoles($module): void
    {
        $path = $module->rbac['roles_path'] ?? null;
        if (!$path) return;

        $roles = Role::all()->mapWithKeys(function ($role) {
            return [$role->name => [
                'style'       => $role->style ?? 'primary',
                'permissions' => $role->permissions->pluck('name')->sort()->values()->toArray(),
            ]];
        })->toArray();

        static::writeJson($module->basePath . '/' . $path, $roles);
    }

    protected static function writeJson(string $fullPath, array $data): void
    {
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
