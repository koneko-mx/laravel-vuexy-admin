@props([
    'uid' => uniqid(),
    'id' => '',
    'model' => '',
    'name' => '',
    'label' => '',
    'labelClass' => 'form-label',
    'placeholder' => '',
    'options' => [],
    'selected' => null,
    'class' => '',
    'parentClass' => '',
    'multiple' => false,
    'disabled' => false,
    'prefixLabel' => null,
    'suffixLabel' => null,
    'buttonBefore' => null,
    'buttonAfter' => null,
    'inline' => false,    // Si es en línea
    'labelCol' => 2,      // Columnas que ocupa el label (Bootstrap grid)
    'inputCol' => 10,     // Columnas que ocupa el input (Bootstrap grid)
    'helperText' => '',   // Texto de ayuda opcional
    'select2' => false,   // Activar Select2 automáticamente
])

@php
    $name = $name ?: $model;
    $inputId = $id ?: ($uid ? str_replace('.', '_', $name) . '_' . $uid : $name);
    $placeholder = $placeholder ?: 'Seleccione ' . strtolower($label);
    $errorClass = $errors->has($model) ? 'is-invalid' : '';
    $options = is_array($options) ? collect($options) : $options;
    $select2Class = $select2 ? 'select2' : '';  // Agrega la clase select2 si está habilitado
@endphp

<div class="{{ $inline ? 'row' : 'mb-4' }} {{ $parentClass }} fv-row">
    @if($label != null)
        <label for="{{ $inputId }}" class="{{ $inline ? 'col-md-' . $labelCol : '' }} {{ $labelClass }}">{{ $label }}</label>
    @endif

    <div class="{{ $inline ? 'col-md-' . $inputCol : '' }}">
        <div class="input-group {{ $prefixLabel || $suffixLabel || $buttonBefore || $buttonAfter ? 'input-group-merge' : '' }}">
            @if ($buttonBefore)
                <button class="btn btn-outline-primary waves-effect" type="button">{{ $buttonBefore }}</button>
            @endif

            @if ($prefixLabel)
                <label class="input-group-text" for="{{ $inputId }}">{{ $prefixLabel }}</label>
            @endif

            <select
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="form-select {{ $errorClass }} {{ $class }} {{ $select2Class }}"
                {{ $multiple ? 'multiple' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $model ? "wire:model=$model" : '' }}
                {{ $select2 ? 'data-live-search="true"' : '' }}
            >
                @if (!$multiple && $placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach ($options as $key => $value)
                    <option value="{{ $key }}" {{ (string) $key === (string) $selected ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>

            @if ($suffixLabel)
                <label class="input-group-text" for="{{ $inputId }}">{{ $suffixLabel }}</label>
            @endif

            @if ($buttonAfter)
                <button class="btn btn-outline-primary waves-effect" type="button">{{ $buttonAfter }}</button>
            @endif
        </div>

        @if ($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endif

        @if ($errors->has($model))
            <span class="text-danger">{{ $errors->first($model) }}</span>
        @endif
    </div>
</div>
