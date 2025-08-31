@props([
    'saveLabel'   => 'Guardar cambios',
    'cancelLabel' => 'Cancelar',
    'cancelClick' => 'resetForm',   // wire:click del Cancel
    'loadingText' => 'Guardando...',
    'align'       => 'end',         // start|center|end
])

@php
    $alignClass = ['start'=>'text-start','center'=>'text-center','end'=>'text-end'][$align] ?? 'text-end';
@endphp

<div class="row">
    <div class="col-12 {{ $alignClass }} mb-4">
        <x-vuexy-admin::button.basic
            type="submit"
            variant="primary"
            size="sm"
            icon="ti ti-device-floppy"
            class="btn-save mt-2 mr-2"
            no-waves
            label="Guardar cambios"
            disabled
        />
        <x-vuexy-admin::button.basic
            variant="secondary"
            size="sm"
            icon="ti ti-rotate-2"
            class="btn-cancel mt-2 mr-2"
            :label="$cancelLabel"
            :wire:click="$cancelClick"
            disabled
        />
    </div>
</div>
