@props([
    'type' => 'primary', // Tipos: primary, secondary, success, danger, warning, info, dark
    'outline' => false,
    'dismissible' => false,
    'icon' => null,
])

@php
    $alertClass = $outline ? "alert-outline-{$type}" : "alert-{$type}";
@endphp

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible' : '' }} d-flex align-items-center" role="alert">
    @if ($icon)
        <span class="alert-icon rounded me-2">
            <i class="{{ $icon }}"></i>
        </span>
    @endif
    <div>
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
