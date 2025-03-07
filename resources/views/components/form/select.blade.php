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
    'size' => '', // Tamaño del select (small, large)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Activar Select2 automáticamente
    'select2' => false,

    // Prefijos y sufijos en input-group
    'prefixLabel' => null,
    'suffixLabel' => null,
    'buttonBefore' => null,
    'buttonAfter' => null,

    // Diseño de disposición (columnas)
    'inline' => false,
    'labelCol' => 4,
    'inputCol' => 8,

    // Texto de ayuda
    'helperText' => '',

    // Opciones del select
    'options' => [],

    // Atributos adicionales
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Configuración de valores base**
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);
    $placeholder = $attributes->get('placeholder', 'Seleccione ' . strtolower($label));
    $selected = $attributes->get('selected', null);

    // **Manejo de errores**
    $errorKey = $livewireModel ?: $name;
    $hasError = $errors->has($errorKey);
    $errorClass = $hasError ? 'is-invalid' : '';

    // **Clases dinámicas**
    $select2Class = $select2 ? 'select2' : '';  // Agrega la clase `select2` si está habilitado
    $sizeClass = match ($size) {
        'sm' => 'form-select-sm',
        'lg' => 'form-select-lg',
        default => '',
    };
    $alignClass = match ($align) {
        'center' => 'text-center',
        'end' => 'text-end',
        default => '',
    };

    // **Construcción de clases dinámicas**
    $fullClass = trim("form-select $select2Class $sizeClass $alignClass $errorClass");
    $fullLabelClass = trim(($inline ? 'col-form-label col-md-' . $labelCol : 'form-label') . ' ' . $labelClass);
    $containerClass = trim(($inline ? 'row' : '') . ' fv-row ' . ($mb0 ? '' : 'mb-4') . " $alignClass $parentClass");

    // **Fusionar atributos con clases dinámicas**
    $selectAttributes = $attributes->merge([
        'id' => $inputId,
        'name' => $name,
    ])->class($fullClass);

    // **Detectar si se necesita input-group**
    $requiresInputGroup = $prefixLabel || $suffixLabel || $buttonBefore || $buttonAfter;
@endphp

<div class="{{ $containerClass }}">
    {{-- Etiqueta del select --}}
    @if($label)
        <label for="{{ $inputId }}" class="{{ $fullLabelClass }}">{{ $label }}</label>
    @endif

    @if($inline)
        <div class="col-md-{{ $inputCol }}">
    @endif

        {{-- Input Group (Si tiene prefijo/sufijo) --}}
        @if ($requiresInputGroup)
            <div class="input-group input-group-merge">
                @isset($buttonBefore)
                    <button class="btn btn-outline-primary waves-effect" type="button">{{ $buttonBefore }}</button>
                @endisset

                @if($prefixLabel)
                    <label class="input-group-text" for="{{ $inputId }}">{{ $prefixLabel }}</label>
                @endif

                {{-- Select --}}
                <select {!! $selectAttributes !!} {{ $attributes->get('multiple') ? 'multiple' : '' }} {{ $attributes->get('disabled') ? 'disabled' : '' }} {{ $livewireModel ? "wire:model=$livewireModel" : '' }} {{ $select2 ? 'data-live-search="true"' : '' }}>
                    @if (!$attributes->get('multiple') && $placeholder)
                        <option value="">{{ $placeholder }}</option>
                    @endif
                    @foreach ($options as $key => $value)
                        <option value="{{ $key }}" {{ (string) $key === (string) $selected ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>

                @if($suffixLabel)
                    <label class="input-group-text" for="{{ $inputId }}">{{ $suffixLabel }}</label>
                @endif

                @isset($buttonAfter)
                    <button class="btn btn-outline-primary waves-effect" type="button">{{ $buttonAfter }}</button>
                @endisset
            </div>
        @else
            {{-- Select sin prefijo/sufijo --}}
            <select {!! $selectAttributes !!} {{ $attributes->get('multiple') ? 'multiple' : '' }} {{ $attributes->get('disabled') ? 'disabled' : '' }} {{ $livewireModel ? "wire:model=$livewireModel" : '' }} {{ $select2 ? 'data-live-search="true"' : '' }}>
                @if (!$attributes->get('multiple') && $placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach ($options as $key => $value)
                    <option value="{{ $key }}" {{ (string) $key === (string) $selected ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        @endif

        {{-- Texto de ayuda --}}
        @isset($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endisset

        {{-- Errores de validación --}}
        @if ($hasError)
            <span class="text-danger">{{ $errors->first($errorKey) }}</span>
        @endif

    @if($inline)
        </div>
    @endif
</div>
