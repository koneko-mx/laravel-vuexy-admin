<div>
    <div id="logo-on-light-bg-card-card" class="mb-4">
        <x-vuexy-admin::card.basic title="Logotipo sobre fondo claro" class="mb-2">
            <x-vuexy-admin::form.input
                type="file"
                label="Logotipo sobre fondo claro"
                model="upload_image_logo"
                accept="image/*" />
            <div class="mb-3 text-center align-items-center">
                <div class="justify-content-center align-items-center bg-slate-100 p-4">
                    <img src="{{ $upload_image_logo ? $upload_image_logo->temporaryUrl() : asset('storage/' . $admin_image_logo) }}">
                </div>
            </div>
        </x-vuexy-admin::card.basic>
        <div class="row">
            <div class="col-12 text-end mb-4">
                <x-vuexy-admin::button.basic variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" waves
                    label="Guardar cambios"
                    wire:click="save"
                    disabled="{{ $upload_image_logo === null }}" />
                <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2" waves
                    label="Cancelar"
                    wire:click="loadForm"
                    disabled="{{ $upload_image_logo === null }}" />
            </div>
        </div>
        <div class="notification-container mb-4" wire:ignore></div>
    </div>
</div>
