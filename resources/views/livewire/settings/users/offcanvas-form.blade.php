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

            {{-- Imágen de perfil --}}
            <div class="mb-4">
                <label class="form-label">Imágen de perfil</label>
                @if ($upload_profile_photo && in_array($upload_profile_photo->getMimeType(), ['image/jpeg', 'image/png', 'image/webp']))
                    <div class="text-center mb-2">
                        <img src="{{ $upload_profile_photo->temporaryUrl() }}" alt="Vista previa" class="img-thumbnail mx-auto" style="max-height: 300px;">
                    </div>
                @endif
                <x-vuexy-admin::form.input type="file" :uid="$uniqueId" model="upload_profile_photo" accept="image/*" />
            </div>

            {{-- Contraseña --}}
            <x-vuexy-admin::form.input-password :uid="$uniqueId" model="password" icon="ti ti-lock" />

            {{-- Roles --}}
            <x-vuexy-admin::form.select :uid="$uniqueId" model="roles" label="Roles" multiple :options="$rolesOptions" class="select2 form-select" />
            <hr>
        </x-vuexy-admin::form>
    </x-vuexy-admin::offcanvas.basic>
</div>

@push('page-script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const uniqueId    = '{{ $uniqueId }}';
        const offcanvasId = '{{ $offcanvasId }}';
        const tagName     = '{{ Str::kebab($tagName) }}';
        const myOffcanvas = document.getElementById(offcanvasId);

        // Asegúrate de no agregar múltiples veces el listener
        const hideAndReopen = () => {
            let myOffcanvas = document.getElementById(offcanvasId);
            let offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(myOffcanvas);

            const onHidden = () => {
                myOffcanvas.removeEventListener('hidden.bs.offcanvas', onHidden);
                offcanvasInstance.show();
            };

            myOffcanvas.addEventListener('hidden.bs.offcanvas', onHidden);
            offcanvasInstance.hide();
        };

        // Inizar Select2
        const initializeSelect2 = () => {
            let rolesSelect = document.getElementById(`roles_${uniqueId}`),
                myOffcanvas = document.getElementById(offcanvasId);

            $(rolesSelect)
                .select2({
                    dropdownAutoWidth: true,
                    width: '100%',
                    placeholder: 'Selecciona los roles',
                    dropdownParent: myOffcanvas
                })
                .on('select2:clear', () => {
                    @this.roles = [];
                })
                .on('select2:select', (e) => {
                    @this.roles.push(e.params.data.id);
                });
        };

        // Inicializar el formulario
        const initializeUserForm = () => {
            setTimeout(initializeSelect2, 10);
        };


        // Evento para inicializar el formulario cuando se abre el offcanvas
        myOffcanvas.addEventListener('show.bs.offcanvas', function () {
            initializeUserForm();
            attachPasswordToggles();
        });


        // Evento para recargar el formulario
        Livewire.on('refresh-user-form', hideAndReopen);
    });
</script>
@endpush