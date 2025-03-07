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
    'size' => '', // Tamaño del radio (sm, lg)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Modo de visualización
    'switch' => false, // Convertir en switch
    'switchType' => 'default', // 'default' o 'square'
    'color' => 'primary', // Colores personalizados (Bootstrap)
    'stacked' => false, // Apilar radios en lugar de inline
    'inline' => false, // Mostrar radios en línea

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
        'sm' => 'form-check-sm',
        'lg' => 'form-check-lg',
        default => '',
    };

    $switchTypeClass = $switchType === 'square' ? 'switch-square' : '';
    $switchColorClass = "switch-{$color}";
    $radioColorClass  = "form-check-{$color}";

    // **Fusionar atributos con clases dinámicas**
    $radioAttributes = $attributes->merge([
        'id' => $inputId,
        'name' => $name,
        'type' => 'radio',
    ]);

    // **Detectar si se usa `inline` o `stacked`**
    $layoutClass = $stacked ? 'switches-stacked' : 'form-check';
@endphp

@if ($switch)
    {{-- ============================ MODO SWITCH ============================ --}}
    <div class="{{ $alignClass }} {{ $inline ? 'd-inline-block' : '' }} {{ $parentClass }} {{ $mb0 ? '' : 'mb-4' }}">
        <label class="switch {{ $switchTypeClass }} {{ $switchColorClass }} {{ $sizeClass }} {{ $labelClass }}">
            <input
                {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $checked ? 'checked' : '' }}
                {!! $radioAttributes->class("switch-input $errorClass")->toHtml() !!}
            />
            {{-- El Switch Slider --}}
            <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
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
    {{-- ============================ MODO RADIO ============================ --}}
    <div class="{{ $layoutClass }} {{ $radioColorClass }} {{ $alignClass }} {{ $sizeClass }} {{ $inline ? 'form-check-inline' : '' }} {{ $parentClass }} {{ $mb0 ? '' : 'mb-4' }}">
        <input
            {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $checked ? 'checked' : '' }}
            {!! $radioAttributes->class("form-check-input $errorClass")->toHtml() !!}
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
