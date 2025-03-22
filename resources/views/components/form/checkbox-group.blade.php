@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelos para Livewire
    'checkboxModel' => '',
    'textModel' => '',

    // Etiqueta y clases de la etiqueta
    'label' => '',
    'labelClass' => '',

    // Clases generales
    'align' => 'start',
    'size' => '', // Tamaño del input (sm, lg)
    'mb0' => false, // Remover margen inferior
    'parentClass' => '',

    // Elementos opcionales antes/después del input
    'prefix' => null,
    'suffix' => null,

    // Íconos dentro del input
    'icon' => null, // Ícono fijo
    'clickableIcon' => null, // Ícono con botón

    // Configuración del checkbox
    'checked' => false,
    'disabled' => false,

    // Configuración del input de texto
    'disableOnOffCheckbox' => true, // Deshabilita input hasta que el checkbox esté activo
    'focusOnCheck' => true, // Hace foco en el input al activar el checkbox

    // Texto de ayuda
    'helperText' => '',

    // Atributos adicionales para el input de texto
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Generación de Name, ID y Model**
    $livewireCheckbox = $checkboxModel ? "wire:model=$checkboxModel" : '';
    $livewireText = $textModel ? "wire:model=$textModel" : '';

    $nameCheckbox = $checkboxModel;
    $nameText = $attributes->get('name', $textModel);

    $checkboxId = $attributes->get('id', 'chk_' . $uid);
    $textInputId = 'txt_' . $uid;

    // **Placeholder del input de texto**
    $placeholder = $attributes->get('placeholder', 'Ingrese información');

    // **Clases dinámicas**
    $sizeClass = match ($size) {
        'sm' => 'form-control-sm',
        'lg' => 'form-control-lg',
        default => '',
    };

    $fullClass = trim("form-control $sizeClass");

    // **Fusionar atributos del input de texto**
    $inputAttributes = $attributes->merge([
        'id' => $textInputId,
        'name' => $nameText,
        'placeholder' => $placeholder,
    ])->class($fullClass);
@endphp

{{-- ============================ CHECKBOX CON INPUT GROUP ============================ --}}
<div class="mb-4 {{ $parentClass }} fv-row">
    @if ($label)
        <label for="{{ $checkboxId }}" class="{{ $labelClass }}">{{ $label }}</label>
    @endif

    <div class="input-group">
        {{-- Checkbox dentro del input-group-text --}}
        <div class="input-group-text">
            <input
                id="{{ $checkboxId }}"
                name="{{ $nameCheckbox }}"
                type="checkbox"
                {{ $livewireCheckbox }}
                {{ $disabled ? 'disabled' : '' }}
                class="form-check-input mt-0"
                onchange="toggleInputState('{{ $checkboxId }}', '{{ $textInputId }}', {{ $focusOnCheck ? 'true' : 'false' }}, {{ $disableOnOffCheckbox ? 'true' : 'false' }})"
            >
        </div>

        {{-- Prefijo opcional --}}
        @isset($prefix)
            <span class="input-group-text">{{ $prefix }}</span>
        @endisset

        {{-- Ícono fijo opcional --}}
        @isset($icon)
            <span class="input-group-text"><i class="{{ $icon }}"></i></span>
        @endisset

        {{-- Input de texto dentro del mismo grupo --}}
        <input
            {{ $livewireText }}
            {!! $inputAttributes !!}
            @if ($disableOnOffCheckbox) disabled @endif
            aria-label="Text input with checkbox"
        >

        {{-- Sufijo opcional --}}
        @isset($suffix)
            <span class="input-group-text">{{ $suffix }}</span>
        @endisset

        {{-- Ícono clickeable opcional --}}
        @isset($clickableIcon)
            <button type="button" class="input-group-text cursor-pointer">
                <i class="{{ $clickableIcon }}"></i>
            </button>
        @endisset
    </div>

    {{-- Texto de ayuda opcional --}}
    @if ($helperText)
        <div class="form-text">{{ $helperText }}</div>
    @endif
</div>

{{-- ============================ JAVASCRIPT PURO ============================ --}}
<script>
    function toggleInputState(checkboxId, inputId, focusOnCheck, disableOnOffCheckbox) {
        let checkbox = document.getElementById(checkboxId);
        let textInput = document.getElementById(inputId);

        if (!checkbox || !textInput) return;

        // Deshabilitar o habilitar el input de texto
        if (disableOnOffCheckbox) {
            textInput.disabled = !checkbox.checked;
        }

        // Enfocar automáticamente el input si está habilitado
        if (focusOnCheck && checkbox.checked) {
            textInput.focus();
        }
    }
</script>
