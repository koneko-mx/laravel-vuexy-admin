<?php

namespace Koneko\VuexyAdmin\Livewire\Roles;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Livewire\Component;

class RoleCards extends Component
{
    public $roles = [];
    public $permissions = [];
    public $roleId;
    public $name;
    public $style;
    public $title;
    public $btn_submit_text;
    public $permissionsInputs = [];
    public $destroyRoleId;

    protected $listeners = ['saveRole', 'deleteRole'];

    public function mount()
    {
        $this->loadRolesAndPermissions();
        $this->dispatch('reloadForm');
    }

    private function loadRolesAndPermissions()
    {
        $this->roles = Auth::user()->hasRole('SuperAdmin') ?
            Role::all() :
            Role::where('name', '!=', 'SuperAdmin')->get();

        // Obtener todos los permisos
        $permissions = Permission::all()->map(function ($permission) {
            $name = $permission->name;
            $action = substr($name, strrpos($name, '.') + 1);

            return [
                'group_name' => $permission->group_name,
                'sub_group_name' => $permission->sub_group_name,
                $action => $name // Agregar la acción directamente al array
            ];
        })->groupBy('group_name'); // Agrupar los permisos por grupo


        // Procesar los permisos agrupados para cargarlos en el componente
        $permissionsInputs = [];

        $this->permissions = $permissions->map(function ($groupPermissions) use (&$permissionsInputs) {
            $permission = [
                'group_name' => $groupPermissions[0]['group_name'], // Tomar el grupo del primer permiso del grupo
                'sub_group_name' => $groupPermissions[0]['sub_group_name'], // Tomar la descripción del primer permiso del grupo
            ];

            // Agregar todas las acciones al permissionsInputs y al permission
            foreach ($groupPermissions as $permissionData) {
                foreach ($permissionData as $key => $value) {
                    if ($key !== 'sub_group_name' && $key !== 'group_name') {
                        $permissionsInputs[str_replace('.', '_', $value)] = false;
                        $permission[$key] = $value;
                    }
                }
            }

            return $permission;
        });

        $this->permissionsInputs = $permissionsInputs;
    }

    public function loadRoleData($action, $roleId = false)
    {
        $this->resetForm();

        $this->title = 'Agregar un nuevo rol';
        $this->btn_submit_text = 'Crear nuevo rol';

        if ($roleId) {
            $role = Role::findOrFail($roleId);

            switch ($action) {
                case 'view':
                    $this->title = $role->name;
                    $this->name = $role->name;
                    $this->style = $role->style;
                    $this->dispatch('deshabilitarFormulario');
                    break;

                case 'update':
                    $this->title = 'Editar rol';
                    $this->btn_submit_text = 'Guardar cambios';
                    $this->roleId = $roleId;
                    $this->name = $role->name;
                    $this->style = $role->style;
                    $this->dispatch('habilitarFormulario');
                    break;

                case 'clone':
                    $this->style = $role->style;
                    $this->dispatch('habilitarFormulario');
                    break;

                default:
                    break;
            }

            foreach ($role->permissions as $permission) {
                $this->permissionsInputs[str_replace('.', '_', $permission->name)] = true;
            }
        }

        $this->dispatch('reloadForm');
    }

    public function loadDestroyRoleData() {}

    public function saveRole()
    {
        $permissions = [];

        foreach ($this->permissionsInputs as $permission => $value) {
            if ($value === true)
                $permissions[] = str_replace('_', '.', $permission);
        }

        if ($this->roleId) {
            $role = Role::find($this->roleId);

            $role->name  = $this->name;
            $role->style = $this->style;

            $role->save();

            $role->syncPermissions($permissions);
        } else {
            $role = Role::create([
                'name'  => $this->name,
                'style' => $this->style,
            ]);

            $role->syncPermissions($permissions);
        }

        $this->loadRolesAndPermissions();

        $this->dispatch('modalHide');
        $this->dispatch('reloadForm');
    }

    public function deleteRole()
    {
        $role = Role::find($this->destroyRoleId);

        if ($role)
            $role->delete();

        $this->loadRolesAndPermissions();

        $this->dispatch('modalDeleteHide');
        $this->dispatch('reloadForm');
    }

    private function resetForm()
    {
        $this->roleId = '';
        $this->name = '';
        $this->style = '';

        foreach ($this->permissionsInputs as $key => $permission) {
            $this->permissionsInputs[$key] = false;
        }
    }

    public function render()
    {
        return view('vuexy-admin::livewire.roles.cards');
    }
}
