@props([
    // Identificador único
    'uid' => uniqid(),

    // Modelos para Livewire
    'radioModel' => '',
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

    // Configuración del radio
    'checked' => false,
    'disabled' => false,
    'name' => '', // Grupo del radio

    // Configuración del input de texto
    'disableOnOffRadio' => true, // Deshabilita input hasta que el radio esté seleccionado
    'focusOnCheck' => true, // Hace foco en el input al seleccionar el radio

    // Texto de ayuda
    'helperText' => '',

    // Atributos adicionales para el input de texto
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
])

@php
    // **Generación de Name, ID y Model**
    $livewireRadio = $radioModel ? "wire:model=$radioModel" : '';
    $livewireText = $textModel ? "wire:model=$textModel" : '';

    $nameRadio = $attributes->get('name', $radioModel ?: $name);
    $nameText = $attributes->get('name', $textModel);

    $radioId = $attributes->get('id', 'radio_' . $uid);
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

{{-- ============================ RADIO BUTTON CON INPUT GROUP ============================ --}}
<div class="{{ $mb0 ? '' : 'mb-4' }} {{ $parentClass }} fv-row">
    @if ($label)
        <label for="{{ $radioId }}" class="{{ $labelClass }}">{{ $label }}</label>
    @endif

    <div class="input-group">
        {{-- Radio dentro del input-group-text --}}
        <div class="input-group-text">
            <input
                id="{{ $radioId }}"
                name="{{ $nameRadio }}"
                type="radio"
                {{ $livewireRadio }}
                {{ $disabled ? 'disabled' : '' }}
                class="form-check-input fv-row mt-0"
                onchange="toggleRadioInputState('{{ $radioId }}', '{{ $textInputId }}', {{ $focusOnCheck ? 'true' : 'false' }}, {{ $disableOnOffRadio ? 'true' : 'false' }})"
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
            @if ($disableOnOffRadio) disabled @endif
            aria-label="Text input with radio button"
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
    function toggleRadioInputState(radioId, inputId, focusOnCheck, disableOnOffRadio) {
        let radio = document.getElementById(radioId);
        let textInput = document.getElementById(inputId);

        if (!radio || !textInput) return;

        // Deshabilitar o habilitar el input de texto
        if (disableOnOffRadio) {
            textInput.disabled = !radio.checked;
        }

        // Enfocar automáticamente el input si está habilitado
        if (focusOnCheck && radio.checked) {
            textInput.focus();
        }
    }
</script>
