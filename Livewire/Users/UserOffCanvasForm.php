<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Koneko\VuexyAdmin\Livewire\Form\AbstractFormOffCanvasComponent;
use Koneko\VuexyAdmin\Models\User;

/**
 * Class UserOffCanvasForm
 *
 * Componente Livewire para gestionar almacenes.
 * Extiende la clase AbstractFormOffCanvasComponent e implementa validaciones dinámicas,
 * manejo de formularios, eventos y actualizaciones en tiempo real.
 *
 * @package Koneko\VuexyAdmin\Livewire\Users
 */
class UserOffCanvasForm extends AbstractFormOffCanvasComponent
{
    /**
     * Propiedades del formulario relacionadas con el usuario.
     */
    public $code,
        $name,
        $last_name,
        $email,
        $status;

    /**
     * Eventos de escucha de Livewire.
     *
     * @var array
     */
    protected $listeners = [
        'editUsers' => 'loadFormModel',
        'confirmDeletionUsers' => 'loadFormModelForDeletion',
    ];

    /**
     * Definición de tipos de datos que se deben castear.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @return string
     */
    protected function focusOnOpen(): string
    {
        return 'name';
    }

    // ===================== VALIDACIONES =====================

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
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                    'name'  => ['required', 'string', 'max:96'],
                ];

            case 'delete':
                return [
                    'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
                ];

            default:
                return [];
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        return [
            'code' => 'código de usuario',
            'name' => 'nombre del usuario',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre del usuario es obligatorio.',
        ];
    }

    /**
     * Ruta de la vista asociada con este formulario.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.users.offcanvas-form';
    }
}
