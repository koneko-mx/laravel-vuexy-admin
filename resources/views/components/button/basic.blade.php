@props([
    'type' => 'button', // Tipo de botón: button, submit, reset (solo para botones)
    'href' => null, // URL si es un enlace
    'route' => null, // URL si es un enlace
    'target' => '_self', // Target del enlace (_self, _blank, etc.)
    'label' => '', // Texto del botón
    'size'    => 'md', // Tamaño: xs, sm, md, lg, xl
    'variant' => 'primary', // Color del botón (primary, secondary, success, danger, warning, info, dark)
    'labelStyle' => false, // Usa `btn-label-*`
    'outline'    => false, // Habilitar estilo outline
    'textStyle'  => false, // Habilitar estilo de texto
    'rounded'    => false, // Habilitar bordes redondeados
    'block'      => false, // Convertir en botón de ancho completo
    'waves'      => true, // Habilitar efecto Vuexy (waves-effect)
    'icon'         => '', // Clases del ícono (ej: 'ti ti-home')
    'iconOnly'     => false, // Botón solo con ícono
    'iconPosition' => 'left', // Posición del ícono: left, right
    'active'   => false, // Activar estado de botón
    'disabled' => false, // Deshabilitar botón
    'attributes' => new \Illuminate\View\ComponentAttributeBag([]), // Atributos adicionales
])

@php
    // Generar clases dinámicas
    $classes = [
        'btn',
        $labelStyle ? "btn-label-$variant" : '',
        $outline ? "btn-outline-$variant" :  '',
        $textStyle ? "btn-text-$variant" : '',
        $labelStyle || $outline || $textStyle ? '' : "btn-$variant",
        $rounded ? 'rounded-pill' : '',
        $block ? 'd-block w-100' : '',
        $waves ? 'waves-effect' : '',
        $size !== 'md' ? "btn-$size" : '',
        $active ? 'active' : '',
        $disabled ? 'disabled' : '',
        $iconOnly ? 'btn-icon' : '',
    ];
@endphp

@if ($href || $route)
    {{-- Si es un enlace --}}
    <a {{ $attributes->merge(['class' => implode(' ', array_filter($classes)), 'href' => ($href?? route($route)), 'target' => $target]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} {{ $label ? 'me-2' : '' }}"></i>
        @endif
        {{ $label }}
        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} {{ $label ? 'ms-2' : '' }}"></i>
        @endif
    </a>
@else
    {{-- Si es un botón --}}
    <button type="{{ $type }}" {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} {{ $label ? 'me-2' : '' }}"></i>
        @endif
        {{ $label }}
        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} {{ $label ? 'ms-2' : '' }}"></i>
        @endif
    </button>
@endif
