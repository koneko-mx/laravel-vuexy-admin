@props([
    'uid' => uniqid(),
    'id' => '',
    'model' => '',
    'name' => '',
    'type' => 'checkbox', // checkbox o radio
    'title' => '',
    'description' => '',
    'icon' => null, // Clases de iconos (ej: ti ti-rocket)
    'image' => null, // URL de imagen
    'checked' => false,
    'disabled' => false,
    'helperText' => '',     // Texto de ayuda opcional
])

@php
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $name ?: $livewireModel;
    $inputId = $id ?: ($uid ? $name . '_' . $uid : $name);
    $errorClass = $errors->has($model) ? 'is-invalid' : '';
    $checkedAttribute = $checked ? 'checked' : '';
    $visualContent = $icon
        ? "<i class='{$icon}'></i>"
        : ($image ? "<img src='{$image}' alt='{$title}' class='img-fluid rounded'>" : '');
@endphp

<div class="mb-4 form-check custom-option custom-option-icon {{ $checked ? 'checked' : '' }}">
    <label class="form-check-label custom-option-content" for="{{ $inputId }}">
        <span class="custom-option-body">
            {!! $visualContent !!}
            <span class="custom-option-title">{{ $title }}</span>
            @if($description)
                <small>{{ $description }}</small>
            @endif
        </span>
        <input
            type="{{ $type }}"
            class="form-check-input {{ $errorClass }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            wire:model="{{ $model }}"
            {{ $checked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
        >
    </label>

    @if ($helperText)
        <div class="form-text">{{ $helperText }}</div>
    @endif

    @error($model)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
