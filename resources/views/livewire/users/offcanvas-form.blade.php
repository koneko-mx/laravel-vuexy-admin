<div>
    <x-vuexy-admin::offcanvas.basic :id="$offcanvasId" :tag-name="$tagName">
        <x-vuexy-admin::form :uid="$uniqueId" :id="$formId" :mode="$mode" wireSubmit="onSubmit">
            <x-slot name="actions">
                <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
            </x-slot>
            {{-- Usuario --}}
            <div class="row">
                <x-vuexy-admin::form.input :uid="$uniqueId" model="code" label="Código de usuario" icon="ti ti-tag" parent-class="col-md-8" autocomplete="off" />
            </div>
            <x-vuexy-admin::form.input :uid="$uniqueId" model="name" label="Nombre(s)" />
            <x-vuexy-admin::form.input :uid="$uniqueId" model="last_name" label="Apellidos" />
            <hr>

            {{-- Teléfonos y Correos --}}
            <x-vuexy-admin::form.input type="tel" :uid="$uniqueId" model="tel" label="Teléfono" icon="ti ti-phone" phoneMode="both" />
            <x-vuexy-admin::form.input type="email" :uid="$uniqueId" model="email" label="Correo electrónico" icon="ti ti-mail" autocomplete="email" inputmode="email" />
            <hr>


            <x-vuexy-admin::form.textarea :uid="$uniqueId" model="notes" label="Notas / Observaciones" />
            <hr>

            {{-- Estado del Centro de Trabajo --}}
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="is_partner" label="Es socio" switch />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="is_employee" label="Es empleado" switch />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="is_prospect" label="Es prospecto" switch />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="is_customer" label="Es cliente" switch />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="is_provider" label="Es proveedor" switch />
            <hr>

        </x-vuexy-admin::form>
    </x-vuexy-admin::offcanvas.basic>
</div>

@push('page-script')
    <script>
        // Evento para inicializar el formulario cuando se carga la página
        document.addEventListener("DOMContentLoaded", function () {
            const initializeUserForm = () => {

            };

            var myOffcanvas = document.getElementById('{{ $offcanvasId }}');

            myOffcanvas.addEventListener('show.bs.offcanvas', function () {
                initializeUserForm();
            });
        });

    </script>
@endpush
