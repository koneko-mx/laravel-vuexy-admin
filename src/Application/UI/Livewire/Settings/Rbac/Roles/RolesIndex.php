<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesIndex extends Component
{
    use WithPagination;

    public $roleName;
    public $selectedRole;
    public $permissions = [];
    public $availablePermissions;

    public function mount()
    {
        $this->availablePermissions = Permission::all();
    }

    public function createRole()
    {
        $this->validate([
            'roleName' => 'required|unique:roles,name'
        ]);

        $role = Role::create(['name' => $this->roleName]);
        $this->reset(['roleName']);
        session()->flash('message', 'Rol creado con éxito.');
    }

    public function selectRole($roleId)
    {
        $this->selectedRole = Role::find($roleId);
        $this->permissions = $this->selectedRole->permissions->pluck('id')->toArray();
    }

    public function updateRolePermissions()
    {
        if ($this->selectedRole) {
            $this->selectedRole->syncPermissions($this->permissions);
            session()->flash('message', 'Permisos actualizados correctamente.');
        }
    }

    public function deleteRole($roleId)
    {
        Role::find($roleId)->delete();
        session()->flash('message', 'Rol eliminado.');
    }

    public function render()
    {
        return view('vuexy-admin::livewire.settings.rbac.roles.index', [
            'roles' => Role::paginate(10)
        ]);
    }
}
