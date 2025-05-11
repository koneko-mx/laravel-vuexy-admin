<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\Rbac\Permissions;

use Koneko\VuexyAdmin\Support\Livewire\Components\Form\AbstractFormOffCanvasComponent;
use Illuminate\Validation\Rule;
use Koneko\VuexyAdmin\Models\PermissionMeta;

/**
 * Class PermissionOffcanvasForm
 *
 * Componente Livewire para gestionar permisos.
 * Extiende AbstractFormOffCanvasComponent para proporcionar una interfaz
 * eficiente para la gestión de permisos en el ERP.
 *
 * @package App\Http\Livewire\Forms
 */
class PermissionOffcanvasForm extends AbstractFormOffCanvasComponent
{
    /**
     * Propiedades del formulario.
     *
     * @var string|null
     */
    public $name, $group_name, $sub_group_name, $action, $guard_name = 'web';

    /**
     * Eventos de escucha de Livewire.
     *
     * @var array
     */
    protected $listeners = [
        'editPermission' => 'loadFormModel',
        'confirmDeletionPermission' => 'loadFormModelForDeletion',
    ];

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    protected function model(): string
    {
        return PermissionMeta::class;
    }

    /**
     * Valores por defecto para el formulario.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'guard_name' => 'web',
        ];
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @return string
     */
    protected function focusColumnOnOpen(): string
    {
        return 'name';
    }

    /**
     * Define reglas de validación dinámicas basadas en el modo actual.
     *
     * @param string $mode El modo actual del formulario ('create', 'edit', 'delete').
     * @return array
     */
    protected function dynamicRules(string $mode): array
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                return [
                    'name' => ['required', 'string', Rule::unique('permissions', 'name')->ignore($this->id)],
                    'group_name' => ['nullable', 'string'],
                    'sub_group_name' => ['nullable', 'string'],
                    'action' => ['nullable', 'string'],
                    'guard_name' => ['required', 'string'],
                ];

            case 'delete':
                return ['confirmDeletion' => 'accepted'];

            default:
                return [];
        }
    }

    /**
     * Define los atributos personalizados para los errores de validación.
     *
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        return [
            'name' => 'nombre del permiso',
        ];
    }

    /**
     * Define los mensajes de error personalizados para la validación.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique' => 'Este permiso ya existe.',
        ];
    }

    /**
     * Carga el formulario con datos de un permiso específico.
     *
     * @param int $id
     */
    public function loadFormModel($id): void
    {
        parent::loadFormModel($id);
    }

    /**
     * Carga el formulario para eliminar un permiso específico.
     *
     * @param int $id
     */
    public function loadFormModelForDeletion($id): void
    {
        parent::loadFormModelForDeletion($id);
    }

    /**
     * Define las opciones de los selectores desplegables.
     *
     * @return array
     */
    protected function options(): array
    {
        return [];
    }

    /**
     * Ruta de la vista asociada con este formulario.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.settings.rbac.permissions.offcanvas-form';
    }
}
