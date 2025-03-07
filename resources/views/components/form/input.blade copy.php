@props([
    'uid' => uniqid(),


    'model' => '',


    'label' => '',
    'labelClass' => '',


    'class' => '',

    'align' => 'start',
    'size' => '',
    'mb-0' => false,





    'parentClass' => '',


    'prefix' => null,
    'suffix' => null,

    'icon' => null,
    'clickableIcon' => null,

    'inline' => false,

    'labelCol' => 4,
    'inputCol' => 8,

    'floatLabel' => false,
    'helperText' => '',

    'attributes' => new \Illuminate\View\ComponentAttributeBag([]), // Atributos adicionales
])

@php
    // Configuración dinámica de atributos y clases CSS
    $livewireModel = $attributes->get('wire:model', $model); // Permitir uso directo de wire:model en el atributo
    $name = $name ?: $livewireModel; // Si no se proporciona el nombre, toma el nombre del modelo
    $inputId = $id ?: ($uid ? $name . '_' . $uid : $name); // ID generado si no se proporciona uno


    // Obtener los atributos actuales en un array
    $attributesArray = array_merge([
        'type' => $type,
        'id' => $inputId,
        'name' => $name,
    ]);


    // Agregar wire:model solo si existe
    if ($livewireModel) {
        $attributesArray['wire:model'] = $livewireModel;
    }


    $attributesArray = array_merge($attributesArray, $attributes->getAttributes());


    // Reconstruir el ComponentAttributeBag con los atributos modificados
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag($attributesArray);

    dump($inputAttributes);


    // Manejo de errores de validación
    $errorKey = $livewireModel ?: $name;
    $errorClass = $errors->has($errorKey) ? 'is-invalid' : '';

    // Definir el tamaño del input basado en la clase seleccionada
    $sizeClass = $size === 'small' ? 'form-control-sm' : ($size === 'large' ? 'form-control-lg' : '');

    // Alineación del texto
    $alignClass = match ($align) {
        'center' => 'text-center',
        'end' => 'text-end',
        default => ''
    };

    // Clases combinadas para el input
    $fullClass = trim("form-control $sizeClass $alignClass $errorClass $class");

    // Detectar si se necesita usar input-group
    $requiresInputGroup = $prefix || $suffix || $icon || $clickableIcon;
@endphp

{{-- Input oculto sin estilos --}}
@if($type === 'hidden')
    <input type="hidden" id="{{ $inputId }}" name="{{ $name }}" {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
     />
@elseif($floatLabel)
    {{-- Input con etiqueta flotante --}}
    <div class="form-floating mb-4 {{ $parentClass }}">
        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
            class="{{ $fullClass }}"
            placeholder="{{ $placeholder ?: 'Ingrese ' . strtolower($label) }}"
            {{ $step ? "step=$step" : '' }}
            {{ $max ? "maxlength=$max" : '' }}
            {{ $min ? "minlength=$min" : '' }}
            {{ $pattern ? "pattern=$pattern" : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $multiple ? 'multiple' : '' }} />
        <label for="{{ $inputId }}">{{ $label }}</label>

        @if ($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endif

        @if ($errors->has($errorKey))
            <span class="text-danger">{{ $errors->first($errorKey) }}</span>
        @endif
    </div>
@else
    {{-- Input con formato clásico --}}
    <div class="{{ $inline ? 'row' : '' }} {{ $parentClass }} fv-row mb-4">
        @isset($label)
            <label for="{{ $inputId }}" class="{{ $inline ? 'col-form-label col-md-' . $labelCol : 'form-label' }} {{ $labelClass }}">{{ $label }}</label>
        @endisset
        <div class="{{ $inline ? 'col-md-' . $inputCol : '' }}">
            @if ($requiresInputGroup)
                <div class="input-group input-group-merge">
                    @if ($prefix)
                        <span class="input-group-text">{{ $prefix }}</span>
                    @endif
                    @if ($icon)
                        <span class="input-group-text"><i class="{{ $icon }}"></i></span>
                    @endif
                    <input
                        type="{{ $type }}"
                        id="{{ $inputId }}"
                        name="{{ $name }}"
                        {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
                        class="{{ $fullClass }}"
                        placeholder="{{ $placeholder ?: 'Ingrese ' . strtolower($label) }}"
                        {{ $step ? "step=$step" : '' }}
                        {{ $max ? "maxlength=$max" : '' }}
                        {{ $min ? "minlength=$min" : '' }}
                        {{ $pattern ? "pattern=$pattern" : '' }}
                        {{ $disabled ? 'disabled' : '' }}
                        {{ $multiple ? 'multiple' : '' }} />
                    @if ($suffix)
                        <span class="input-group-text">{{ $suffix }}</span>
                    @endif
                    @if ($clickableIcon)
                        <button type="button" class="input-group-text cursor-pointer">
                            <i class="{{ $clickableIcon }}"></i>
                        </button>
                    @endif
                </div>
            @else
                {{-- Input sin prefijo o íconos --}}
                <input
                    type="{{ $type }}"
                    id="{{ $inputId }}"
                    name="{{ $name }}"
                    {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
                    class="{{ $fullClass }}"
                    placeholder="{{ $placeholder ?: 'Ingrese ' . strtolower($label) }}"
                    {{ $step ? "step=$step" : '' }}
                    {{ $max ? "maxlength=$max" : '' }}
                    {{ $min ? "minlength=$min" : '' }}
                    {{ $pattern ? "pattern=$pattern" : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $multiple ? 'multiple' : '' }} />
            @endif
            @if ($helperText)
                <small class="form-text text-muted">{{ $helperText }}</small>
            @endif
            @if ($errors->has($errorKey))
                <span class="text-danger">{{ $errors->first($errorKey) }}</span>
            @endif
        </div>
    </div>
@endif
