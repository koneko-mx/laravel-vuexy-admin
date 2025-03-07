<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Koneko\VuexyAdmin\Models\User;

use Livewire\Component;

class UserIndex extends Component
{
    public $statuses;
    public $status_options;

    public $rows_roles = [];
    public $roles_options = [];
    public $roles_html_select;

    public $total, $enabled, $disabled;

    public $indexAlert;

    public $userId, $name, $email, $password, $roles, $status, $photo, $src_photo;
    public $modalTitle;
    public $btnSubmitTxt;

    public function mount()
    {
        $this->modalTitle   = 'Crear usuario nuevo';
        $this->btnSubmitTxt = 'Crear usuario';

        $this->statuses = [
            User::STATUS_ENABLED  => ['title' => 'Activo', 'class' => 'badge bg-label-' . User::$statusListClass[User::STATUS_ENABLED]],
            User::STATUS_DISABLED => ['title' => 'Deshabilitado', 'class' => 'badge bg-label-' . User::$statusListClass[User::STATUS_DISABLED]],
            User::STATUS_REMOVED  => ['title' => 'Eliminado', 'class' => 'badge bg-label-' . User::$statusListClass[User::STATUS_REMOVED]],
        ];

        $roles = Role::whereNotIn('name', ['Patient', 'Doctor'])->get();

        $this->roles_html_select = "<select id=\"UserRole\" class=\"form-select text-capitalize\"><option value=\"\"> Selecciona un rol </option>";

        foreach ($roles as $role) {
            $this->rows_roles[$role->name] = "<span class=\"badge bg-label-" . $role->style . " mx-1\">" . $role->name . "</span>";

            if (Auth::user()->hasRole('SuperAdmin') || $role->name != 'SuperAdmin') {
                $this->roles_html_select .= "<option value=\"" . $role->name . "\" class=\"text-capitalize\">" . $role->name . "</option>";
                $this->roles_options[$role->name] = $role->name;
            }
        }

        $this->roles_html_select .= "</select>";

        $this->status_options = [
            User::STATUS_ENABLED  => User::$statusList[User::STATUS_ENABLED],
            User::STATUS_DISABLED => User::$statusList[User::STATUS_DISABLED],
        ];
    }

    public function countUsers()
    {
        $this->total = User::count();
        $this->enabled = User::where('status', User::STATUS_ENABLED)->count();
        $this->disabled = User::where('status', User::STATUS_DISABLED)->count();
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->indexAlert = '';
        $this->modalTitle   = 'Editar usuario: ' . $id;
        $this->btnSubmitTxt = 'Guardar cambios';

        $this->userId    = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->password  = '';
        $this->roles     = $user->roles->pluck('name')->toArray();
        $this->src_photo = $user->profile_photo_url;
        $this->status    = $user->status;

        $this->dispatch('openModal');
    }

    public function delete($id)
    {
        $user = User::find($id);

        if ($user) {
            // Eliminar la imagen de perfil si existe
            if ($user->profile_photo_path)
                Storage::disk('public')->delete($user->profile_photo_path);

            // Eliminar el usuario
            $user->delete();

            $this->indexAlert = '<div class="alert alert-warning alert-dismissible" role="alert">Se eliminó correctamente el usuario.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

            $this->dispatch('refreshUserCount');
            $this->dispatch('afterDelete');
        } else {
            $this->indexAlert = '<div class="alert alert-danger alert-dismissible" role="alert">Usuario no encontrado.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }

    public function render()
    {
        return view('vuexy-admin::livewire.users.index', [
            'users' => User::paginate(10),
        ]);
    }
}
