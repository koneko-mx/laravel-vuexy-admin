@props([
    'tagName' => '',
    'mode'    => 'create',
])

@php
    $helperTag = Str::kebab($tagName);

    $onclick = "window.formHelpers['{$helperTag}'].reloadOffcanvas('reset')";
@endphp

<div class="pt-3">
    <button type="submit" class="btn btn-submit waves-effect waves mr-3 mb-3"></button>
    <button type="reset" class="btn btn-reset mr-3 mb-3" onclick="{{ $onclick }}" data-bs-dismiss="offcanvas" aria-label="Close">Cancelar</button>
</div>
@if($mode == 'delete')
<x-vuexy-admin::form.checkbox model="confirmDeletion" label="Confirmar eliminación" parentClass="confirm-deletion" data-always-enabled switch switch-type="square" color="danger" size="lg" with-icon />
@endif
