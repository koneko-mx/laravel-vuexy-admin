<div>
    <x-vuexy-admin::offcanvas.basic :id="$offcanvasId" :tag-name="$tagName">
        <x-vuexy-admin::form :id="$formId" :mode="$mode" wireSubmit="onSubmit">
            <x-slot name="actions">
                <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
            </x-slot>

            {{-- Sección: Identificación --}}
            <x-vuexy-admin::form.input :uid="$uniqueId" model="key" label="Clave del parámetro" required icon="ti ti-key" placeholder="Ej: ui.theme" />

            <div class="row">
                <x-vuexy-admin::form.select :uid="$uniqueId" model="category" label="Categoría"
                    :options="[
                        'general' => 'General',
                        'ui' => 'Interfaz',
                        'mail' => 'Correo',
                        'cfdi' => 'CFDI',
                    ]"
                    placeholder="Selecciona una categoría"
                    parentClass="col-md-6"
                />
                <x-vuexy-admin::form.input :uid="$uniqueId" model="user_id" label="ID Usuario (Opcional)"
                    type="number"
                    parentClass="col-md-6"
                    icon="ti ti-user"
                    placeholder="ID del usuario asociado"
                />
            </div>

            <hr>

            {{-- Sección: Valores (solo llena uno) --}}
            <div class="alert" type="info" icon="ti ti-info-circle" class="mb-1">
                Puedes llenar **solo uno** de los valores siguientes según el tipo de configuración.
            </div>

            <x-vuexy-admin::form.input :uid="$uniqueId" model="value_string" label="Valor (Texto Corto)" icon="ti ti-typography" placeholder="Ej: dark" />
            <x-vuexy-admin::form.input :uid="$uniqueId" model="value_integer" label="Valor (Entero)" type="number" icon="ti ti-hash" />
            <x-vuexy-admin::form.input :uid="$uniqueId" model="value_float" label="Valor (Decimal)" type="number" step="0.01" icon="ti ti-percentage" />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="value_boolean" label="Valor (Booleano)" />
            <x-vuexy-admin::form.textarea :uid="$uniqueId" model="value_text" label="Valor (Texto Largo)" placeholder="Valor largo o configuración avanzada en texto" rows="3" />

            <hr>
        </x-vuexy-admin::form>
    </x-vuexy-admin::offcanvas.basic>
</div>

@push('page-script')
    <script>
        // Evento para inicializar el formulario cuando se carga la página
        document.addEventListener("DOMContentLoaded", function () {
            const initializeGlobalSettingsForm = () => {

            };

            var myOffcanvas = document.getElementById('{{ $offcanvasId }}');

            myOffcanvas.addEventListener('show.bs.offcanvas', function () {
                initializeGlobalSettingsForm();
            });
        });

    </script>
@endpush
