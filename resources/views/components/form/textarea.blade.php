@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelo para Livewire
    'model' => '',

    // Etiqueta y clases de la etiqueta
    'label' => '',
    'labelClass' => '',

    // Clases generales
    'align' => 'start', // Alineación del textarea (start, end)
    'size' => 'md', // Tamaño del textarea (sm, md, lg)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Elementos opcionales antes/después del textarea
    'prefix' => null,
    'suffix' => null,

    // Íconos dentro del input
    'prefixIcon' => null, // Ícono fijo a la izquierda
    'prefixClickableIcon' => null, // Ícono con botón a la izquierda
    'suffixIcon' => null, // Ícono fijo a la derecha
    'suffixClickableIcon' => null, // Ícono con botón a la derecha

    // Configuración del textarea
    'rows' => 3,
    'maxlength' => null,
    'autosize' => false, // Autoajuste de altura
    'resize' => true, // Permitir redimensionar

    // Soporte para etiquetas flotantes
    'floating' => false, // Si la etiqueta es flotante

    // Texto de ayuda
    'helperText' => '',

    // Atributos adicionales para el textarea
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Generación de Name, ID y Model**
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);

    // **Placeholder obligatorio si es flotante**
    $placeholder = $attributes->get('placeholder', $floating ? ' ' : 'Ingrese ' . strtolower($label));

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

    // **Control de redimensionamiento**
    $resizeClass = $resize ? '' : 'resize-none';

    // **Fusionar atributos del textarea**
    $textareaAttributes = $attributes->merge([
        'id' => $inputId,
        'name' => $name,
        'rows' => $rows,
        'placeholder' => $placeholder,
        'maxlength' => $maxlength,
    ])->class("form-control $sizeClass $resizeClass $errorClass");

    // **Clases del contenedor flotante**
    $floatingClass = $floating ? 'form-floating' : '';

    // **Detectar si necesita Input-Group**
    $requiresInputGroup = $prefix || $suffix || $prefixIcon || $suffixIcon || $prefixClickableIcon || $suffixClickableIcon;
@endphp

{{-- ============================ TEXTAREA ============================ --}}
<div class="{{ $mb0 ? '' : 'mb-4' }} {{ $parentClass }} {{ $alignClass }} {{ $floatingClass }} fv-row">
    @if (!$floating && $label)
        <label for="{{ $inputId }}" class="{{ $labelClass }}">{{ $label }}</label>
    @endif

    @if ($requiresInputGroup)
        {{-- Textarea con Input-Group --}}
        <div class="input-group">
            {{-- Prefijos (Izquierda) --}}
            @if ($prefix)
                <span class="input-group-text">{{ $prefix }}</span>
            @endif

            @if ($prefixIcon)
                <span class="input-group-text"><i class="{{ $prefixIcon }}"></i></span>
            @endif

            @if ($prefixClickableIcon)
                <button type="button" class="input-group-text cursor-pointer">
                    <i class="{{ $prefixClickableIcon }}"></i>
                </button>
            @endif

            {{-- Textarea --}}
            <textarea
                {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
                {!! $textareaAttributes !!}
            ></textarea>

            {{-- Sufijos (Derecha) --}}
            @if ($suffixClickableIcon)
                <button type="button" class="input-group-text cursor-pointer">
                    <i class="{{ $suffixClickableIcon }}"></i>
                </button>
            @endif

            @if ($suffixIcon)
                <span class="input-group-text"><i class="{{ $suffixIcon }}"></i></span>
            @endif

            @if ($suffix)
                <span class="input-group-text">{{ $suffix }}</span>
            @endif
        </div>
    @else
        {{-- Textarea simple o flotante --}}
        <textarea
            {{ $livewireModel ? "wire:model=$livewireModel" : '' }}
            {!! $textareaAttributes !!}
        ></textarea>
    @endif

    {{-- Etiqueta flotante --}}
    @if ($floating)
        <label for="{{ $inputId }}">{{ $label }}</label>
    @endif

    {{-- Longitud máxima permitida --}}
    @if ($maxlength)
        <small class="form-text float-end text-muted">Máximo {{ $maxlength }} caracteres</small>
    @endif

    {{-- Texto de ayuda --}}
    @if ($helperText)
        <div class="form-text">{{ $helperText }}</div>
    @endif

    {{-- Mensaje de error --}}
    @if ($hasError)
        <span class="text-danger">{{ $errors->first($errorKey) }}</span>
    @endif
</div>

{{-- ============================ JAVASCRIPT PARA AUTOSIZE ============================ --}}
@if ($autosize)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let textarea = document.getElementById('{{ $inputId }}');
            if (textarea) {
                textarea.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                textarea.dispatchEvent(new Event('input')); // Auto-trigger on load
            }
        });
    </script>
@endif
