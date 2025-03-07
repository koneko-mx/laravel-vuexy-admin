<?php

namespace Koneko\VuexyAdmin\Livewire\Permissions;

use Spatie\Permission\Models\Role;

use Livewire\Component;

class PermissionIndex extends Component
{
    public $roles_html_select;
    public $rows_roles;

    public function render()
    {
        // Generamos Select y estilos HTML de roles
        $this->roles_html_select = "<select id=\"UserRole\" class=\"form-select text-capitalize\"><option value=\"\"> Selecciona un rol </option>";

        foreach (Role::all() as $role) {
            $this->rows_roles[$role->name] = "<span class=\"badge bg-label-{$role->style} m-1\">{$role->name}</span>";
            $this->roles_html_select      .= "<option value=\"{$role->name}\" class=\"text-capitalize\">{$role->name}</option>";
        }

        $this->roles_html_select .= "</select>";

        return view('vuexy-admin::livewire.permissions.index');
    }
}
