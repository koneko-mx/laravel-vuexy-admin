<div>
    <form id="mail-sender-response-settings-card">
        <div class="card">
            <h5 class="card-header">Correo electrónicos de salida</h5>
            <div class="card-body">
                <div class="mb-3 fv-row">
                    <label for="from_address" class="form-label">Correo electrónico</label>
                    <input type="text" name="from_address" id="from_address" wire:model='from_address' class="form-control" placeholder="Correo electrónico">
                    @error('from_address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3 fv-row">
                    <label for="from_name" class="form-label">Nombre</label>
                    <input type="text" name="from_name" id="from_name" wire:model='from_name' class="form-control" placeholder="Nombre">
                    @error('from_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <h5 class="mt-6">Correo electrónico de respuesta</h5>
                <div class="mb-3 fv-row">
                    <label for="reply_to_method" class="form-label">Correo electrónico de respuesta</label>
                    <x-vuexy-admin::form.select
                        wire:model='reply_to_method'
                        :options="$reply_email_options"
                        placeholder="Selecciona el correo electrónico de respuesta" />
                    @error('reply_to_method') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="email-custom-div">
                    <div class="mb-3 fv-row">
                        <label for="reply_to_email" class="form-label">Correo electrónico</label>
                        <input type="text" name="reply_to_email" id="reply_to_email" wire:model='reply_to_email' class="form-control" placeholder="Correo electrónico">
                        @error('reply_to_email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="fv-row">
                        <label for="reply_to_name" class="form-label">Nombre</label>
                        <input type="text" name="reply_to_name" id="reply_to_name" wire:model='reply_to_name' class="form-control" placeholder="Nombre">
                        @error('reply_to_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        type="submit"
                        id="save_sender_response_button"
                        class="btn btn-primary btn-sm mt-2 mr-2 waves-effect waves-light"
                        disabled
                        data-loading-text="Guardando...">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        id="cancel_sender_response_button"
                        class="btn btn-secondary btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="loadSettings"
                        disabled>
                        <i class="ti ti-rotate-2 mr-2"></i>
                        Cancelar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container" wire:ignore></div>
        </div>
    </form>
</div>
