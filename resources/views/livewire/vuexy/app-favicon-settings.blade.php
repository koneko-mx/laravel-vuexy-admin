<div>
    <div id="app-favicon-settings-card" class="mb-4">
        <x-vuexy-admin::card.basic title="Favicon" class="mb-2">
            <x-vuexy-admin::form.input
                type="file"
                label="Icono de navegador"
                model="upload_image_favicon"
                accept="image/*" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-16x16 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_16x16) }}">
                        </div>
                        <span class="text-muted mt-1">Navegadores web (16x16)</span>
                    </div>
                </div>
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-76x76 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_76x76) }}">
                        </div>
                        <span class="text-muted mt-1">iPad sin Retina (76x76)</span>
                    </div>
                </div>
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-120x120 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_120x120) }}">
                        </div>
                        <span class="text-muted mt-1">iPhone (120x120)</span>
                    </div>
                </div>
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-152x152 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_152x152) }}">
                        </div>
                        <span class="text-muted mt-1">iPad (152x152)</span>
                    </div>
                </div>
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-180x180 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_180x180) }}">
                        </div>
                        <span class="text-muted mt-1">iPhone con Retina HD (180x180)</span>
                    </div>
                </div>
                <div class="text-center flex flex-col items-center">
                    <div class="mb-3 text-center d-flex flex-column align-items-center">
                        <div class="image-wrapper-192x192 d-flex justify-content-center align-items-center">
                            <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : asset('storage/' . $admin_favicon_192x192) }}">
                        </div>
                        <span class="text-muted mt-1">Android y otros dispositivos móviles (192x192)</span>
                    </div>
                </div>
            </div>
        </x-vuexy-admin::card.basic>
        <div class="row">
            <div class="col-lg-12 text-end">
                <x-vuexy-admin::button.basic
                    variant="primary"
                    size="sm"
                    icon="ti ti-device-floppy"
                    label="Guardar cambios"
                    wire:click="save"
                    :disabled="$upload_image_favicon === null"
                    class="btn-save mt-2 mr-2"
                    waves />
                <x-vuexy-admin::button.basic
                    variant="secondary"
                    size="sm"
                    icon="ti ti-rotate-2"
                    label="Cancelar"
                    wire:click="resetForm"
                    :disabled="$upload_image_favicon === null"
                    class="btn-cancel mt-2 mr-2"
                    waves />
            </div>
        </div>
        <div class="notification-container pt-4" wire:ignore></div>
    </div>
</div>
