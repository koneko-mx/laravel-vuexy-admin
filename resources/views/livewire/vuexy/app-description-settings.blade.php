<div>
    <div id="app-description-settings-card" class="form-custom-listener mb-4">
        <x-vuexy-admin::card.basic title="Datos de la aplicación" class="mb-4">
            <x-vuexy-admin::form.input
                label="Titulo de la aplicación"
                model="app_name"
                placeholder="Nombre corto" />
            <x-vuexy-admin::form.input
                label="Titulo del sitio"
                model="title"
                placeholder="Titulo del sitio" />
            <x-vuexy-admin::form.textarea
                label="Descripción del sitio"
                model="description"
                placeholder="Descripción del sitio" />
        </x-vuexy-admin::card.basic>
        <div class="row">
            <div class="col-lg-12 text-end">
                <x-vuexy-admin::button.basic
                    variant="primary"
                    size="sm"
                    icon="ti ti-device-floppy"
                    label="Guardar cambios"
                    disabled
                    wire:click="save"
                    class="btn-save"
                    waves />
                <x-vuexy-admin::button.basic
                    variant="secondary"
                    size="sm"
                    icon="ti ti-rotate-2"
                    label="Cancelar"
                    disabled
                    wire:click="resetForm"
                    class="btn-cancel"
                    waves />
            </div>
        </div>
        <div class="notification-container pt-4" wire:ignore></div>
    </div>
</div>
