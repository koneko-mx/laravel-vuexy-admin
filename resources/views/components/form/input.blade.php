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

    // Elementos de prefijo
    'prefix' => null,
    'prefixIcon' => null,
    'icon' => null, // Alias para prefixIcon
    'prefixClickable' => false,
    'prefixAction' => null,

    // Elementos de sufijo
    'suffix' => null,
    'suffixIcon' => null,
    'suffixClickable' => false,
    'suffixAction' => null,

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

    // Manejar el alias de icon a prefixIcon
    $prefixIcon = $prefixIcon ?? $icon;

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

    // Verificar si se necesita el input-group
    $hasAddons = $prefix || $prefixIcon || $suffix || $suffixIcon;
@endphp

{{-- Estructura del Input --}}
<div class="{{ $mb0 ? '' : 'mb-4' }} {{ $parentClass }} fv-row">
    {{-- Etiqueta --}}
    @if ($label)
        <label for="{{ $inputId }}" class="form-label {{ $labelClass }}">{{ $label }}</label>
    @endif

    {{-- Input con Prefijos o Sufijos --}}
    @if ($hasAddons)
        <div class="input-group input-group-merge">
            {{-- Prefijo --}}
            @if ($prefix || $prefixIcon)
                @if ($prefixClickable)
                    <button type="button" class="input-group-text cursor-pointer" {{ $prefixAction ? "wire:click=$prefixAction" : '' }}>
                        @if ($prefixIcon)
                            <i class="{{ $prefixIcon }}"></i>
                        @endif
                        @if ($prefix)
                            {{ $prefix }}
                        @endif
                    </button>
                @else
                    <span class="input-group-text">
                        @if ($prefixIcon)
                            <i class="{{ $prefixIcon }}"></i>
                        @endif
                        @if ($prefix)
                            {{ $prefix }}
                        @endif
                    </span>
                @endif
            @endif

            <input {!! $inputAttributes !!} {{ $livewireModel ? "wire:model=$livewireModel" : '' }} />

            {{-- Sufijo --}}
            @if ($suffix || $suffixIcon)
                @if ($suffixClickable)
                    <button type="button" class="input-group-text cursor-pointer" {{ $suffixAction ? "wire:click=$suffixAction" : '' }}>
                        @if ($suffixIcon)
                            <i class="{{ $suffixIcon }}"></i>
                        @endif
                        @if ($suffix)
                            {{ $suffix }}
                        @endif
                    </button>
                @else
                    <span class="input-group-text">
                        @if ($suffixIcon)
                            <i class="{{ $suffixIcon }}"></i>
                        @endif
                        @if ($suffix)
                            {{ $suffix }}
                        @endif
                    </span>
                @endif
            @endif
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