<div>
    <div class="form-custom-listener" id="general-settings-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Información de página web</h5>
                <div class="fv-row mb-3">
                    <label for="admin_title" class="form-label">
                        Titulo del sitio <span class="text-xs">(Nombre completo)</span>
                    </label>
                    <input type="text" id="admin_title" wire:model="admin_title" class="form-control"
                        placeholder="Titulo del sitio">
                    @error('admin_title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="fv-row mb-6">
                    <label for="upload_image_favicon" class="form-label">
                        Icono de página <span class="text-xs">(Favicon)</span>
                    </label>
                    <input type="file" wire:model="upload_image_favicon" id="upload_image_favicon" class="form-control"
                        accept="image/*" />
                    @error('upload_image_favicon')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
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
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        type="button"
                        wire:click="save"
                        class="btn btn-primary btn-save btn-sm mt-2 mr-2 waves-effect waves-light"
                        {{ !$upload_image_favicon ? 'disabled' : '' }}
                        data-loading-text="Guardando...">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        wire:click="loadSettings"
                        class="btn btn-secondary btn-cancel btn-sm mt-2 mr-2 waves-effect waves-light"
                        {{ !$upload_image_favicon ? 'disabled' : '' }}>
                        <i class="ti ti-rotate-2 mr-2"></i>
                        Cancelar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container" wire:ignore></div>
        </div>
    </div>
</div>
