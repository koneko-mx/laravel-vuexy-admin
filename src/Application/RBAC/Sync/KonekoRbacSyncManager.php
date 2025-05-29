<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\RBAC\Sync;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Application\Bootstrap\Manager\KonekoModuleBootManager;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Models\{PermissionMeta, RoleMeta, PermissionGroup};
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class KonekoRbacSyncManager
{
    protected string $moduleName;
    protected string $basePath;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = $moduleName;
        $this->basePath   = $basePath;
    }

    public static function importAll(): void
    {
        foreach (KonekoModuleRegistry::enabled() as $module) {
            $basePath = KonekoModuleBootManager::resolvePath($module, "database/data/rbac");
            File::ensureDirectoryExists($basePath);

            $syncService = new self($module->composerName, $basePath);
            $syncService->import();
        }
    }

    public function import(bool $onlyPermissions = false, bool $onlyRoles = false, bool $overwrite = false): void
    {
        $permissionPath = $this->basePath . '/permissions.json';
        $rolePath       = $this->basePath . '/roles.json';

        // Importar Permisos
        if (!$onlyRoles && File::exists($permissionPath)) {
            $json   = json_decode(File::get($permissionPath), true);

            $moduleKey       = $json['module'] ?? 'undefined';
            $groups          = $json['groups'] ?? [];
            $externar_groups = $json['externar_groups'] ?? [];

            $groupModel = PermissionGroup::firstOrCreate(
                [
                    'module_register' => $this->moduleName,
                    'type'            => 'module',
                    'module' => $moduleKey,
                ],
                [
                    'name'        => $json['name'] ?? ['es' => $moduleKey, 'en' => $moduleKey],
                    'ui_metadata' => $json['_meta'] ?? null,
                    'priority'    => $json['priority'] ?? null,
                ]
            );

            // Importar grupos
            foreach ($groups as $groupKey => $group) {
                $rootModel = PermissionGroup::firstOrCreate(
                    [
                        'module_register' => $this->moduleName,
                        'parent_id'       => $groupModel->id,
                        'type'            => 'root_group',
                        'module' => $moduleKey,
                        'grupo'  => $groupKey,
                    ],
                    [
                        'name'        => $group['name'] ?? ['es' => $groupKey, 'en' => $groupKey],
                        'ui_metadata' => $group['_meta'] ?? null,
                        'priority'    => $group['priority'] ?? null,
                    ]
                );

                // Importar subgrupos
                foreach ($group['sub_groups'] ?? [] as $subGroupKey => $subGroup) {
                    // Vincular subgrupos huérfanos (sin parent_id)
                    $nullSubGroupDb = PermissionGroup::whereNull('parent_id')
                        ->where('type', 'sub_group')
                        ->where('module', $moduleKey)
                        ->where('grupo', $groupKey)
                        ->get();

                    foreach ($nullSubGroupDb as $subGroup) {
                        $subGroup->parent_id = $rootModel->id;
                        $subGroup->save();
                    }

                    $subGroupModel = PermissionGroup::firstOrCreate(
                        [
                            'module_register' => $this->moduleName,
                            'parent_id'       => $rootModel->id,
                            'type'            => 'sub_group',
                            'module'    => $moduleKey,
                            'grupo'     => $groupKey,
                            'sub_grupo' => $subGroupKey,
                        ],
                        [
                            'name'        => $subGroup['name'] ?? ['es' => $subGroupKey, 'en' => $subGroupKey],
                            'ui_metadata' => $subGroup['_meta'] ?? null,
                            'priority'    => $subGroup['priority'] ?? null,
                        ]
                    );

                    // Crear permismos del subgrupo
                    foreach ($subGroup['permissions'] ?? [] as $permission) {
                        PermissionMeta::updateOrCreate(
                            [
                                'name'        => $moduleKey . '.' . $permission['key'],
                                'guard_name'  => $permission['guard_name'] ?? 'web',
                            ],
                            [
                                'group_id'    => $subGroupModel->id,
                                'label'       => $permission['label'] ?? null,
                                'ui_metadata' => isset($permission['_meta']) ? $permission['_meta'] : null,
                                'action'      => $permission['action'] ?? null,
                            ]
                        );
                    }
                }
            }

            // Importar permisos externos
            foreach ($externar_groups as $module) {
                $moduleKey = $module['module'];

                // Procesar grupos externos
                foreach($module['grupos'] as $groupKey => $grupos) {

                    // Procesar subgrupos externos
                    foreach ($grupos as $subGroupKey => $subgrupos) {

                        $parentId = PermissionGroup::where('type', 'sub_group')
                            ->where('module_register', '!=', $this->moduleName)
                            ->where('module', $moduleKey)
                            ->where('grupo', $groupKey)
                            ->first()?->id;

                        $subGroupModel = PermissionGroup::firstOrCreate(
                            [
                                'module_register' => $this->moduleName,
                                'parent_id'       => $parentId,
                                'type'            => 'sub_group',
                                'module'    => $moduleKey,
                                'grupo'     => $groupKey,
                                'sub_grupo' => $subGroupKey,
                            ],
                            [
                                'name'        => $subGroup['name'] ?? ['es' => $subGroupKey, 'en' => $subGroupKey],
                                'ui_metadata' => $subGroup['_meta'] ?? null,
                                'priority'    => $subGroup['priority'] ?? null,
                            ]
                        );

                        // Procesar permisos
                        foreach ($subgrupos['permissions'] as $permission) {

                            PermissionMeta::updateOrCreate(
                                [
                                    'name'        => $moduleKey . '.' . $permission['key'],
                                    'guard_name'  => $permission['guard_name'] ?? 'web',
                                ],
                                [
                                    'group_id'    => $subGroupModel->id,
                                    'label'       => $permission['label'] ?? null,
                                    'ui_metadata' => isset($permission['_meta']) ? $permission['_meta'] : null,
                                    'action'      => $permission['action'] ?? null,
                                ]
                            );

                        }

                        if (!$parentId){
                            Log::info("[RBAC] Permiso externo agregado: {$moduleKey}.{$subGroupKey} con ausencia de componente {$moduleKey}.{$groupKey}");
                        }
                    }
                }
            }
        }

        // Importar Roles
        if (!$onlyPermissions && File::exists($rolePath)) {
            $roles = json_decode(File::get($rolePath), true);

            foreach ($roles as $name => $config) {
                $role = RoleMeta::firstOrCreate(
                    [
                        'name'       => $name,
                        'guard_name' => $config['guard_name'] ?? 'web',
                    ],
                    [
                        'ui_metadata' => $config['_meta'] ?? null,
                    ]
                );

                try {
                    if ($overwrite) {
                        $role->syncPermissions($config['permissions']);

                    } else {
                        $role->givePermissionTo($config['permissions']);
                    }

                } catch (PermissionDoesNotExist $e) {
                    Log::warning("[RBAC] Rol '{$name}' contiene permisos no válidos: " . $e->getMessage());
                }
            }
        }
    }

    public function export(bool $onlyPermissions = false, bool $onlyRoles = false): void
    {
        File::ensureDirectoryExists($this->basePath);

        if (!$onlyRoles) {
            $permissions = PermissionMeta::query()
                ->where('name', 'like', "$this->moduleName.%")
                ->get()
                ->mapWithKeys(fn($perm) => [
                    $perm->name => [
                        'guard_name'  => $perm->guard_name,
                        'action'      => $perm->action,
                        'label'       => $perm->label,
                        'ui_metadata' => $perm->ui_metadata,
                        'group_id'    => $perm->group_id,
                    ]
                ])->toArray();

            File::put("$this->basePath/permissions.json", json_encode($permissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        if (!$onlyPermissions) {
            $roles = RoleMeta::all()->mapWithKeys(function ($role) {
                return [$role->name => [
                    'style'       => $role->ui_metadata['style'] ?? null,
                    'permissions' => $role->permissions->pluck('name')->sort()->values()->all(),
                ]];
            })->toArray();

            File::put("$this->basePath/roles.json", json_encode($roles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    public function publish(bool $force = false): void
    {
        File::ensureDirectoryExists($this->basePath);

        $defaultPermissions = [];
        $defaultRoles = [];

        if (!File::exists("$this->basePath/permissions.json") || $force) {
            File::put("$this->basePath/permissions.json", json_encode($defaultPermissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        if (!File::exists("$this->basePath/roles.json") || $force) {
            File::put("$this->basePath/roles.json", json_encode($defaultRoles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
