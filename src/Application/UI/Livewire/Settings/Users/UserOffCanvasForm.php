<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarImageService;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Livewire\Components\Form\AbstractFormOffCanvasComponent;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class UserOffCanvasForm extends AbstractFormOffCanvasComponent
{
    use WithFileUploads;

    /**
     * Propiedades del formulario relacionadas con el usuario.
     *
     * @var mixed
     */
    public $code,
        $name,
        $last_name,
        $password,
        $roles,
        $email,
        $status;

    public $photoPreview;

    public $upload_profile_photo;
    public $rolesOptions = [];

    public function updatedUploadProfilePhoto()
    {
        $this->dispatch('refresh-user-form');
    }

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    protected function model(): string
    {
        return User::class;
    }

    // ===================== VALIDACIONES =====================

    /**
     * Define reglas de validación dinámicas basadas en el modo actual.
     *
     * @param string $mode El modo actual del formulario ('create', 'edit', 'delete').
     * @return array<string, array<int, mixed>>
     */
    protected function dynamicRules(string $mode): array
    {
        return match ($mode) {
            'create', 'edit' => [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'name'  => ['required', 'string', 'max:96'],
                'last_name' => ['nullable', 'string', 'max:96'],
                'upload_profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp'],
                'password' => ['required', 'string', 'min:8'],
            ],
            'delete' => [
                'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
            ],
            default => [],
        };
    }

    /**
     * Define atributos personalizados para mensajes de validación.
     *
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        return [
            'name' => 'nombre del usuario',
            'last_name' => 'apellido(s) del usuario',
            'upload_profile_photo' => 'imagen de perfil',
        ];
    }

    /**
     * Define mensajes personalizados para errores de validación.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre del usuario es obligatorio.',
        ];
    }

    protected function options(): array
    {
        return [
            'rolesOptions' => Role::all()->pluck('name', 'id')->toArray(),
        ];
    }

    protected function beforeSave(array &$data): void
    {
        if ($this->mode === 'create') {
            $data['password'] = bcrypt($data['password']);
        }

        $data['created_by'] = Auth::user()->id;
        $data['status']     = User::STATUS_ENABLED;
    }

    protected function afterSave(Model $record): void
    {
        // Sincroniza los roles con el usuario
        $record->roles()->sync($this->roles);

        // Generamos el Avatar
        if ($this->upload_profile_photo) {
            app(AvatarImageService::class)->updateProfilePhoto($record, $this->upload_profile_photo);
        }

        $this->upload_profile_photo = null;
    }

    /**
     * Define la ruta de la vista para este formulario.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.settings.users.offcanvas-form';
    }
}
