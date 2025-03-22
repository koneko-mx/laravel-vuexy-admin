<div>
    <div id="logo-on-dark-bg-settings-card" class="mb-4">
        <x-vuexy-admin::card.basic title="Logotipo sobre fondo oscuro" class="mb-2">
            <x-vuexy-admin::form.input
                type="file"
                label="Logotipo sobre fondo oscuro"
                model="upload_image_logo_dark"
                accept="image/*" />
            <div class="mb-3 text-center align-items-center">
                <div class="justify-content-center align-items-center bg-slate-800 p-4">
                    <img src="{{ $upload_image_logo_dark ? $upload_image_logo_dark->temporaryUrl() : asset('storage/' . $admin_image_logo_dark) }}">
                </div>
            </div>
        </x-vuexy-admin::card.basic>
        <div class="row">
            <div class="col-lg-12 text-end">
                <x-vuexy-admin::button.basic
                    variant="primary"
                    size="sm"
                    icon="ti ti-device-floppy"
                    disabled="{{ $upload_image_logo_dark === null }}"
                    label="Guardar cambios"
                    wire:click="save"
                    class="btn-save mt-2 mr-2"
                    waves />
                <x-vuexy-admin::button.basic
                    variant="secondary"
                    size="sm"
                    icon="ti ti-rotate-2"
                    disabled="{{ $upload_image_logo_dark === null }}"
                    label="Cancelar"
                    wire:click="resetForm"
                    class="btn-cancel mt-2 mr-2"
                    waves />
            </div>
        </div>
        <div class="notification-container pt-4" wire:ignore></div>
    </div>
</div>
