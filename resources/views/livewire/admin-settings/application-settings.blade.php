<div>
    <div class="form-custom-listener" id="application-settings-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Información de aplicación</h5>
                <div class="fv-row mb-3">
                    <label for="admin_app_name" class="form-label">
                        Titulo de aplicación <span class="text-xs">(Nombre corto)</span>
                    </label>
                    <input type="text" id="admin_app_name" wire:model="admin_app_name" class="form-control" placeholder="Titulo de aplicación">
                    @error('admin_app_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="fv-row mb-6">
                    <h5>Logotipo tema claro</h5>
                    <div class="fv-row mb-4">
                        <input type="file" wire:model="upload_image_logo" id="upload_image_logo" class="form-control" accept="image/*" />
                        @error('upload_image_logo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-8 text-center align-items-center">
                        <div class="justify-content-center align-items-center bg-slate-100 p-4">
                            <img src="{{ $upload_image_logo ? $upload_image_logo->temporaryUrl() : asset('storage/' . $admin_image_logo) }}">
                        </div>
                    </div>
                    <h5>Logotipo tema obscuro</h5>
                    <div class="fv-row mb-4">
                        <input type="file" wire:model="upload_image_logo_dark" id="upload_image_logo_dark" class="form-control" accept="image/*" />
                        @error('upload_image_logo_dark')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 text-center align-items-center">
                        <div class="justify-content-center align-items-center bg-slate-800 p-4">
                            <img src="{{ $upload_image_logo_dark ? $upload_image_logo_dark->temporaryUrl() : asset('storage/' . $admin_image_logo_dark) }}">
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
                        :disabled="{{ $upload_image_logo === null && $upload_image_logo_dark === null ? 'true' : 'false' }}"
                        data-loading-text="Guardando...">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        wire:click="loadSettings"
                        class="btn btn-secondary btn-cancel btn-sm mt-2 mr-2 waves-effect waves-light"
                        :disabled="{{ $upload_image_logo === null && $upload_image_logo_dark === null ? 'true' : 'false' }}">
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
