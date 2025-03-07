@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelo para Livewire
    'model' => '',

    // Etiqueta y Texto de ayuda
    'message' => '',
    'note' => '',

    // Clases generales
    'size' => 'md', // Tamaño del textarea (xxs, xs, sm, md)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',
])

@php
    // **Configuración de Name, ID y Model**
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);

    // **Manejo de errores**
    $errorKey = $livewireModel ?: $name;
    $hasError = $errors->has($errorKey);
    $errorClass = $hasError ? 'is-invalid' : '';

    // **Clases dinámicas**
    $sizeClassDropzone = match ($size) {
        'sm' => 'dropzone-sm',
        'xs' => 'dropzone-xs',
        'md' => 'dropzone-md',
        default => '',
    };

    $sizeClassButton = match ($size) {
        'sm' => 'btn-sm',
        'xs' => 'btn-xs',
        default => '',
    };
@endphp

<!-- Dropzone (se oculta automáticamente después de la carga) -->
<div class="{{ $mb0 ? '' : 'mb-4 ' }} {{ $sizeClassDropzone }}">
    <div class="dropzone" id="dropzone_{{ $inputId }}"">
        <div class="dz-message">
            @if ($size == 'sm' || $size == 'xs')
                <div class="flex items-center">
                    <span class="btn btn-icon {{ $sizeClassButton }} btn-label-secondary mr-2">
                        <i class="ti ti-upload text-base"></i>
                    </span>
                    <span>
                        {{ $message }}
                        <span class="note needsclick">{!! $note !!}</span>
                    </span>
                </div>
            @else
                {{ $message }}
                <span class="note needsclick">{!! $note !!}</span>
            @endif
        </div>
    </div>
    <input type="file" wire:model="pdf" {{ $livewireModel ? "wire:model=$livewireModel" : '' }} id="{{ $inputId }}" accept="application/pdf" class="d-none">

    {{-- Mensaje de error --}}
    @if ($hasError)
        <span class="text-danger">{{ $errors->first($errorKey) }}</span>
    @endif
</div>
