@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelo de Livewire
    'model' => '',

    // Etiqueta y Clases
    'label' => '',
    'labelClass' => '',
    'placeholder' => '',

    // Clases Generales
    'align' => 'start',
    'size' => '', // Tamaño del input (sm, lg)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Elementos opcionales antes/después del input
    'prefix' => null,
    'suffix' => null,

    // Íconos dentro del input
    'icon' => null,
    'clickableIcon' => null,

    // Configuración especial
    'phoneMode' => false, // "national", "international", "both"

    // Diseño de disposición (columnas)
    'inline' => false,
    'labelCol' => 4,
    'inputCol' => 8,

    // Configuración de etiquetas flotantes y texto de ayuda
    'floatLabel' => false,
    'helperText' => '',

    // Atributos adicionales
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Configuración de Name, ID y Model**
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);
    $type = $attributes->get('type', 'text');

    // **Definir formato de teléfono según `phoneMode`**
    if ($phoneMode) {
        $type = 'tel';
        $attributes = $attributes->merge([
            'autocomplete' => 'tel',
            'inputmode' => 'tel',
        ]);

        switch ($phoneMode) {
            case 'national':
                $attributes = $attributes->merge([
                    'pattern' => '^(?:\D*\d){10,}$',
                    'placeholder' => $placeholder !== false ? ($placeholder ?: 'Ej. (55) 1234-5678') : null,
                ]);
                break;

            case 'international':
                $attributes = $attributes->merge([
                    'pattern' => '^\+?[1-9]\d{1,14}$',
                    'placeholder' => $placeholder !== false ? ($placeholder ?: 'Ej. +52 1 55 1234-5678') : null,
                ]);
                break;

            case 'both':
                $attributes = $attributes->merge([
                    'pattern' => '(^(?:\D*\d){10,}$)|(^\+?[1-9]\d{1,14}$)',
                    'placeholder' => $placeholder !== false ? ($placeholder ?: 'Ej. (55) 1234-5678 o +52 1 55 1234-5678') : null,
                ]);
                break;
        }
    }

    // **Manejo del Placeholder si no lo estableció `phoneMode`**
    if (!$attributes->has('placeholder')) {
        if ($placeholder === false) {
            // No agregar `placeholder`
            $placeholderAttr = [];
        } elseif (empty($placeholder)) {
            // Generar automáticamente desde el `label`
            $placeholderAttr = ['placeholder' => 'Ingrese ' . strtolower($label)];
        } else {
            // Usar `placeholder` definido manualmente
            $placeholderAttr = ['placeholder' => $placeholder];
        }

        // Fusionar el placeholder si no fue definido en `phoneMode`
        $attributes = $attributes->merge($placeholderAttr);
    }

    // **Manejo de errores**
    $errorKey = $livewireModel ?: $name;
    $hasError = $errors->has($errorKey);
    $errorClass = $hasError ? 'is-invalid' : '';

    // **Clases dinámicas**
    $sizeClass = match ($size) {
        'sm' => 'form-control-sm',
        'lg' => 'form-control-lg',
        default => '',
    };

    $alignClass = match ($align) {
        'center' => 'text-center',
        'end' => 'text-end',
        default => '',
    };

    // **Fusionar atributos**
    $inputAttributes = $attributes->merge([
        'type' => $type,
        'id' => $inputId,
        'name' => $name,
    ])->class("form-control $sizeClass $alignClass $errorClass");
@endphp

{{-- Estructura del Input --}}
<div class="{{ $mb0 ? '' : 'mb-4' }} {{ $parentClass }}">
    {{-- Etiqueta --}}
    @if ($label)
        <label for="{{ $inputId }}" class="form-label {{ $labelClass }}">{{ $label }}</label>
    @endif

    {{-- Input con Prefijos, Sufijos o Íconos --}}
    @if ($prefix || $suffix || $icon || $clickableIcon)
        <div class="input-group input-group-merge">
            @isset($prefix)
                <span class="input-group-text">{{ $prefix }}</span>
            @endisset

            @isset($icon)
                <span class="input-group-text"><i class="{{ $icon }}"></i></span>
            @endisset

            <input {!! $inputAttributes !!} {{ $livewireModel ? "wire:model=$livewireModel" : '' }} />

            @isset($suffix)
                <span class="input-group-text">{{ $suffix }}</span>
            @endisset

            @isset($clickableIcon)
                <button type="button" class="input-group-text cursor-pointer">
                    <i class="{{ $clickableIcon }}"></i>
                </button>
            @endisset
        </div>
    @else
        {{-- Input Simple --}}
        <input {!! $inputAttributes !!} {{ $livewireModel ? "wire:model=$livewireModel" : '' }} />
    @endif

    {{-- Texto de ayuda --}}
    @if ($helperText)
        <div class="form-text">{{ $helperText }}</div>
    @endif

    {{-- Mensajes de error --}}
    @if ($hasError)
        <span class="text-danger">{{ $errors->first($errorKey) }}</span>
    @endif
</div>
