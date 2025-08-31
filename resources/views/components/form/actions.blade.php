@props([
  'saveLabel' => 'Guardar cambios',
  'cancelLabel' => 'Cancelar',
  'cancelWireClick' => null,  // ej. "resetForm"
  'size' => 'sm',
  'saveVariant' => 'primary',
  'cancelVariant' => 'secondary',
  'saveIcon' => 'ti ti-device-floppy',
  'cancelIcon' => 'ti ti-rotate-2',
  'saveClass' => 'btn-save mt-2 me-2',
  'cancelClass' => 'btn-cancel mt-2',
  'showCancel' => true,
])

<div class="row">
  <div class="col-12 text-end mb-4">
    <x-vuexy-admin::button.basic
      type="submit"
      :variant="$saveVariant"
      :size="$size"
      :icon="$saveIcon"
      :class="$saveClass . ' waves-effect waves-light'"
      :label="$saveLabel"
      data-loading-text="Guardando..."
      disabled
    />
    @if($showCancel)
      <x-vuexy-admin::button.basic
        :variant="$cancelVariant"
        :size="$size"
        :icon="$cancelIcon"
        :class="$cancelClass . ' waves-effect waves-light'"
        :label="$cancelLabel"
        @if($cancelWireClick) wire:click="{{ $cancelWireClick }}" @endif
        disabled
      />
    @endif
  </div>
</div>
