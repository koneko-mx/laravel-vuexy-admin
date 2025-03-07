<div x-data="{
        changeSmtpSettings: @entangle('change_smtp_settings'),
        saveButtonDisabled: @entangle('save_button_disabled'),
    }">
    <form id="mail-smtp-settings-card">
        <div class="card mb-6">
            <h5 class="card-header">Servidor saliente de correo electrónico</h5>
            <div class="card-body">
                <div class="mb-3">
                    <x-vuexy-admin::form.checkbox
                        wire:model='change_smtp_settings'
                        parent_class='form-switch'>
                        Cambiar configuración
                    </x-form.checkbox>
                </div>
                <div class="mb-3 fv-row">
                    <label for="host" class="form-label">Servidor de correo saliente (SMTP)</label>
                    <input type="text" name="host" id="host" wire:model='host' class="form-control" placeholder="Servidor de salida" :disabled="!changeSmtpSettings">
                    @error('host') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3 fv-row">
                    <label for="port" class="form-label">Puerto SMTP</label>
                    <input type="number" name="port" id="port" wire:model='port' class="form-control" placeholder="Puerto SMTP" :disabled="!changeSmtpSettings">
                    @error('port') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3 fv-row">
                    <label for="encryption" class="form-label">Encriptación</label>
                    <x-vuexy-admin::form.select
                        wire:model='encryption'
                        :options="$encryption_options"
                        :disabled="!$change_smtp_settings"
                        placeholder="Selecciona el método de encriptación" />
                    @error('encryption') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 fv-row">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" name="username" id="username" wire:model='username' class="form-control" placeholder="Usuario" autocomplete="username" :disabled="!changeSmtpSettings">
                    @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="fv-row">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" wire:model='password' class="form-control" placeholder="Contraseña" autocomplete="current-password" :disabled="!changeSmtpSettings">
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        type="button"
                        id="test_smtp_connection_button"
                        class="btn btn-success btn-sm mt-2 mr-2 waves-effect waves-light"
                        :disabled="!changeSmtpSettings"
                        data-loading-text="Realizando prueba...">
                        <i class="ti ti-flask mr-2"></i>
                        Realizar una prueba
                    </button>
                    <button
                        type="submit"
                        id="save_smtp_connection_button"
                        class="btn btn-primary btn-sm mt-2 mr-2 waves-effect waves-light"
                        :disabled="saveButtonDisabled"
                        wire:click="save"
                        data-loading-text="Guardando...">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        id="cancel_smtp_connection_button"
                        class="btn btn-secondary btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="loadSettings"
                        :disabled="!changeSmtpSettings">
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
