@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelo de Livewire (si aplica)
    'model' => '',

    // Etiqueta y clases de la etiqueta
    'label' => '',
    'labelClass' => '',

    // Clases generales
    'align' => 'start',
    'size' => 'md', // Tamaño del checkbox/switch: sm, md, lg
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Estilos del checkbox
    'switch' => false, // Cambiar a modo switch
    'switchType' => 'default', // 'default' o 'square'
    'color' => 'primary', // Bootstrap color: primary, secondary, success, danger, etc.
    'withIcon' => false, // Si el switch usa íconos en On/Off

    // Diseño de disposición
    'inline' => false, // Modo inline en lugar de bloque

    // Texto de ayuda
    'helperText' => '',

    // Atributos adicionales
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Configuración de valores base**
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);
    $checked = $attributes->get('checked', false);
    $disabled = $attributes->get('disabled', false);

    // **Manejo de errores**
    $errorKey = $livewireModel ?: $name;
    $hasError = $errors->has($errorKey);
    $errorClass = $hasError ? 'is-invalid' : '';

    // **Clases dinámicas**
    $alignClass = match ($align) {
        'center' => 'text-center',
        'end' => 'text-end',
        default => '',
    };

    $sizeClass = match ($size) {
        'sm' => 'switch-sm',
        'lg' => 'switch-lg',
        default => '',
    };

    $switchTypeClass = $switchType === 'square' ? 'switch-square' : '';
    $switchColorClass = "switch-{$color}";
    $checkColorClass  = "form-check-{$color}";

    // **Fusionar atributos con clases dinámicas**
    $checkboxAttributes = $attributes->merge([
        'id' => $inputId,
        'name' => $name,
        'type' => 'checkbox',
    ]);

    // **Detectar si se usa input-group**
    $requiresInputGroup = $attributes->get('inline');
@endphp

@if ($switch)
    {{-- ============================ MODO SWITCH ============================ --}}
    <div class="{{ $alignClass }} {{ $inline ? 'd-inline-block' : '' }} {{ $parentClass }} {{ $mb0 ? '' : 'mb-2' }} fv-row">
        <label class="switch {{ $switchTypeClass }} {{ $switchColorClass }} {{ $sizeClass }} {{ $labelClass }}">
            <input
                {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $checked ? 'checked' : '' }}
                {!! $checkboxAttributes->class("switch-input $errorClass")->toHtml() !!}
            />
            {{-- El Switch Slider --}}
            <span class="switch-toggle-slider">
                @if ($withIcon)
                    <span class="switch-on">
                        <i class="ti ti-check"></i>
                    </span>
                    <span class="switch-off">
                        <i class="ti ti-x"></i>
                    </span>
                @endif
            </span>
            {{-- Etiqueta textual a la derecha del switch --}}
            <span class="switch-label">
                {{ $label }}
            </span>
        </label>

        {{-- Texto de ayuda y error --}}
        @isset($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endisset
        @if ($hasError)
            <span class="text-danger">{{ $errors->first($errorKey) }}</span>
        @endif
    </div>

@else
    {{-- ============================ MODO CHECKBOX ============================ --}}
    <div class="form-check {{ $checkColorClass }} {{ $alignClass }} {{ $sizeClass }} {{ $inline ? 'form-check-inline' : '' }} {{ $parentClass }} {{ $mb0 ? '' : 'mb-4' }} fv-row">
        <input
            {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $checked ? 'checked' : '' }}
            {!! $checkboxAttributes->class("form-check-input $errorClass")->toHtml() !!}
        >

        <label for="{{ $inputId }}" class="form-check-label {{ $labelClass }}">
            {{ $label }}
        </label>

        {{-- Texto de ayuda y error --}}
        @isset($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endisset
        @if ($hasError)
            <span class="text-danger">{{ $errors->first($errorKey) }}</span>
        @endif
    </div>
@endif
