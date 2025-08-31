<div>
    <div id="app-description-card" class="form-custom-listener mb-4">
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
            <div class="col-12 text-end mb-4">
                <x-vuexy-admin::button.basic variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save" waves
                    label="Guardar cambios"
                    wire:click="save"
                    disabled />
                <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel" waves
                    label="Cancelar"
                    wire:click="resetForm"
                    disabled />
            </div>
        </div>
        <div class="notification-container mb-4" wire:ignore></div>
    </div>
</div>
