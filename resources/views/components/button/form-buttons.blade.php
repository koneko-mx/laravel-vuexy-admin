@props([
    'label' => '',
    'mode'  => 'create',
])

@php
    $btnLabel = match($mode) {
        'create' => 'Crear ' . $label,
        'edit'   => 'Guardar cambios',
        'delete' => 'Eliminar'
    };

    $btnType = match($mode) {
        'create' => 'success',
        'edit'   => 'warning',
        'delete' => 'danger'
    };

@endphp

<div class="pt-3">
    <button type="submit" class="btn btn-{{ $btnType }} btn-submit waves-effect waves mr-3 mb-3">{{ $btnLabel }}</button>
    <button type="reset" class="btn btn-reset mr-3 mb-3">Cancelar</button>
</div>
@if($mode == 'delete')
<x-vuexy-admin::form.checkbox model="confirmDeletion" label="Confirmar eliminación" parentClass="confirm-deletion" data-always-enabled switch switch-type="square" color="danger" size="lg" with-icon />
@endif
