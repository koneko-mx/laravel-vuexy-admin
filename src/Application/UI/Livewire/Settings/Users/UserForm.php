<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Users;

use Koneko\VuexyAdmin\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Koneko\VuexySatCatalogs\Models\{Estado,Pais,RegimenFiscal};
use Koneko\VuexyAdmin\Support\Livewire\Components\Form\AbstractFormComponent;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarImageService;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class UserForm extends AbstractFormComponent
{
    use WithFileUploads;

    public $name, $last_name, $password, $roles, $email, $status;
    public $upload_profile_photo;
    public $photoPreview;

    public $rolesOptions = [];

    public function mount(string $mode = 'create', mixed $id = null): void
    {
        parent::mount($mode, $id);

        $this->rolesOptions = Role::all()->pluck('name', 'id')->toArray();
    }


    public function updatedUploadProfilePhoto(): void
    {
        $this->dispatch('refresh-user-form');
    }

    // ===================== MÉTODOS OBLIGATORIOS =====================

    protected function model(): string
    {
        return User::class;
    }

    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.settings.users.form';
    }

    // ===================== VALIDACIONES =====================

    protected function dynamicRules(string $mode): array
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                return [
                    'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->id)],
                    'name' => ['required', 'string', 'max:96'],
                    'last_name' => ['nullable', 'string', 'max:96'],
                    'upload_profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp'],
                    'password' => [$this->mode === 'edit' ? 'nullable' : 'required', 'string', 'min:8'],
                ];

            case 'delete':
                return [
                    'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
                ];

            default:
                return [];
        }
    }

    protected function attributes(): array
    {
        return [
            'name' => 'nombre del usuario',
            'last_name' => 'apellido(s)',
            'upload_profile_photo' => 'imagen de perfil',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre del usuario es obligatorio.',
        ];
    }

    // ===================== ACCIONES =====================
    protected function beforeSave(array &$data): void
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);

        } else {
            unset($data['password']);
        }

        $data['created_by'] = Auth::id();
        $data['status'] = User::STATUS_ENABLED;
    }

    protected function afterSave(Model $record): void
    {
        if ($this->roles) {
            $record->roles()->sync($this->roles);
        }

        if ($this->upload_profile_photo) {
            app(AvatarImageService::class)->updateProfilePhoto($record, $this->upload_profile_photo);

            $this->upload_profile_photo = null;
        }
    }

}
