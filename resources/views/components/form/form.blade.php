@props([
    'id' => uniqid(),                // ID único del formulario
    'uniqueId' => '',                // ID único del formulario
    'mode' => 'create',              // Modo actual ('create', 'edit', 'delete')
    'method' => 'POST',              // Método del formulario (POST, GET, PUT, DELETE)
    'action' => '',                  // URL de acción
    'enctype' => false,              // Para envío de archivos
    'wireSubmit' => false,           // Usar wire:submit.prevent
    'class' => '',                   // Clases adicionales para el formulario
    'actionPosition' => 'bottom',    // Posición de acciones: top, bottom, both, none
    'whitOutId' => false,            // Excluir el ID del formulario
    'whitOutMode' => false,          // Excluir el modo del formulario
])

@php
    $formAttributes = [
        'id' => $id,
        'method' => $method,
        'action' => $action,
        'class' => "fv-plugins-bootstrap5 $class",
    ];

    if ($wireSubmit) {
        $formAttributes['wire:submit.prevent'] = $wireSubmit;
    }

    if ($enctype) {
        $formAttributes['enctype'] = 'multipart/form-data';
    }
@endphp

<form {{ $attributes->merge($formAttributes) }}>
    @if (!$whitOutId)
        <x-vuexy-admin::form.input :uid="$uniqueId" type="hidden" model="id" />
    @endif
    @if (!$whitOutMode)
        <x-vuexy-admin::form.input :uid="$uniqueId" type="hidden" model="mode" />
    @endif
    @if ($mode !== 'delete' && in_array($actionPosition, ['top', 'both']))
        <div class="notification-container mb-4"></div>
        <div class="form-actions mb-4">
            {{ $actions ?? '' }}
        </div>
    @endif
    <div class="form-body">
        {{ $slot }}
    </div>
    @if (in_array($actionPosition, ['bottom', 'both']))
        <div class="notification-container mb-4"></div>
        <div class="form-actions mt-4">
            {{ $actions ?? '' }}
        </div>
    @endif
</form>
