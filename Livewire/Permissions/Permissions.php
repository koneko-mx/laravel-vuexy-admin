<?php

namespace Koneko\VuexyAdmin\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Permissions extends Component
{
    public $permissionName;

    public function createPermission()
    {
        $this->validate([
            'permissionName' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $this->permissionName]);
        session()->flash('message', 'Permiso creado con éxito.');
        $this->reset('permissionName');
    }

    public function deletePermission($id)
    {
        Permission::find($id)->delete();
        session()->flash('message', 'Permiso eliminado.');
    }

    public function render()
    {
        return view('livewire.permissions', [
            'permissions' => Permission::all()
        ]);
    }
}
