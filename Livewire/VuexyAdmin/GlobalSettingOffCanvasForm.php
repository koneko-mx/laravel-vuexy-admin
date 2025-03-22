<?php

namespace Koneko\VuexyAdmin\Livewire\VuexyAdmin;

use Illuminate\Validation\Rule;
use Koneko\VuexyAdmin\Models\Setting;
use Koneko\VuexyAdmin\Livewire\Form\AbstractFormOffCanvasComponent;

/**
 * Class GlobalSettingOffCanvasForm
 *
 * Componente Livewire para gestionar parámetros globales del sistema.
 * Permite almacenar configuraciones personalizadas y valores múltiples tipos.
 *
 * @package Koneko\VuexyAdmin\Livewire\Settings
 */
class GlobalSettingOffCanvasForm extends AbstractFormOffCanvasComponent
{
    public $id, $key, $category, $user_id,
        $value_string, $value_integer, $value_boolean,
        $value_float, $value_text;

    public $confirmDeletion;

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_integer' => 'integer',
        'value_float'   => 'float',
    ];

    protected $listeners = [
        'editGlobalSetting' => 'loadFormModel',
        'confirmDeletionGlobalSetting' => 'loadFormModelForDeletion',
    ];

    protected function model(): string
    {
        return Setting::class;
    }

    protected function fields(): array
    {
        return [
            'key', 'category', 'user_id',
            'value_string', 'value_integer', 'value_boolean',
            'value_float', 'value_text'
        ];
    }

    protected function defaults(): array
    {
        return [
            'category' => 'general',
        ];
    }

    protected function focusOnOpen(): string
    {
        return 'key';
    }

    protected function dynamicRules(string $mode): array
    {
        if ($mode === 'delete') {
            return ['confirmDeletion' => 'accepted'];
        }

        $uniqueRule = Rule::unique('settings', 'key')
            ->where(fn ($q) => $q
                ->where('user_id', $this->user_id)
                ->where('category', $this->category)
            );

        if ($mode === 'edit') {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'key'            => ['required', 'string', $uniqueRule],
            'category'       => ['nullable', 'string', 'max:96'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'value_string'   => ['nullable', 'string', 'max:255'],
            'value_integer'  => ['nullable', 'integer'],
            'value_boolean'  => ['nullable', 'boolean'],
            'value_float'    => ['nullable', 'numeric'],
            'value_text'     => ['nullable', 'string'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'key' => 'clave de configuración',
            'category' => 'categoría',
        ];
    }

    protected function messages(): array
    {
        return [
            'key.required' => 'La clave del parámetro es obligatoria.',
            'key.unique'   => 'Ya existe una configuración con esta clave en esa categoría.',
        ];
    }

    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.global-settings.offcanvas-form';
    }
}
