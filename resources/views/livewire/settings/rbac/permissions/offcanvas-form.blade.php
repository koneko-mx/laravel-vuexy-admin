<div>
    <x-vuexy-admin::offcanvas.basic :id="$offcanvasId" :tag-name="$tagName">
        <x-vuexy-admin::form :uid="$uniqueId" :id="$formId" :mode="$mode" wireSubmit="onSubmit">
            <x-slot name="actions">
                <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
            </x-slot>
            {{-- Usuario --}}
            <x-vuexy-admin::form.input :uid="$uniqueId" model="name" label="Nombre(s)" />
            <x-vuexy-admin::form.input :uid="$uniqueId" model="last_name" label="Apellidos" />
            {{-- Correos electrónicos --}}
            <x-vuexy-admin::form.input type="email" :uid="$uniqueId" model="email" label="Correo electrónico" icon="ti ti-mail" autocomplete="email" inputmode="email" />

            {{-- Contraseña --}}
            <x-vuexy-admin::form.input type="password" :uid="$uniqueId" model="password" label="Contraseña" icon="ti ti-lock" autocomplete="new-password" />


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
