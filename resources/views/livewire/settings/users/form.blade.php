<div>
    <x-vuexy-admin::form id="{{ $formId }}" :mode="$mode" wireSubmit="onSubmit" actionPosition="both">
        <x-slot name="actions">
            <x-vuexy-admin::button.form-buttons :mode="$mode" :label="$singularName" />
        </x-slot>
        <div class="row">
            <div class="col-lg-4">
                {{-- Identificación --}}
                <x-vuexy-admin::card.basic title="Identificación">
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="name" label="Nombre de usuario" />
                </x-vuexy-admin::card.basic>

                {{-- Configuraciones --}}
                <x-vuexy-admin::card.basic title="Configuraciones">
                    <x-vuexy-admin::form.checkbox uid="random" model="status" label="Habilitar sucursal" switch="true" />
                </x-vuexy-admin::card.basic>
            </div>
            <div class="col-lg-4">
                {{-- Información de contacto --}}
                <x-vuexy-admin::card.basic title="Información de contacto">
                    <x-vuexy-admin::form.input type="email" :uid="$uniqueId" model="email" label="Correo electrónico" icon="ti ti-mail" autocomplete="email" inputmode="email" />
                </x-vuexy-admin::card.basic>
            </div>
        </div>
    </x-vuexy-admin::form>
</div>

@push('page-script')
    <script>
        const initializeUserForm = (mode) => {

        }

        // Evento para inicializar el formulario
        document.addEventListener("DOMContentLoaded", () => {
            document.addEventListener('on-failed-validation-user', (event) => {
                setTimeout(() => {
                    initializeUserForm('{{ $mode }}');
                }, 10);
            });

            initializeUserForm('{{ $mode }}');
        });
    </script>
@endpush
