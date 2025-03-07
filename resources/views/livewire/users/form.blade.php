<div>
    <x-vuexy-admin::form id="{{ $formId }}" :mode="$mode" wireSubmit="onSubmit" actionPosition="both">
        <x-slot name="actions">
            <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
        </x-slot>
        <div class="row">
            <div class="col-lg-4">
                {{-- Identificación --}}
                <x-vuexy-admin::card.basic title="Identificación">
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="code" label="Identificador único" icon="ti ti-tag" placeholder="UID code" autofocus autocomplete="off" />
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="name" label="Nombre de la sucursal" autocomplete="organization" />
                    <x-vuexy-admin::form.textarea :uid="$uniqueId" model="description" label="Descripción" placeholder="Descripción de la sucursal" :autosize=true />
                </x-vuexy-admin::card.basic>

                {{-- Series de facturación --}}
                <x-vuexy-admin::card.basic title="Series de facturación">
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="serie_ingresos" label="Serie para Ingresos" inline=true :labelCol=6 :inputCol=6 maxlength="5" autocomplete="off" />
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="serie_egresos" label="Serie para Egresos" inline=true :labelCol=6 :inputCol=6 maxlength="5" autocomplete="off" />
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="serie_pagos" label="Serie para Pagos" inline=true :labelCol=6 :inputCol=6 maxlength="5" autocomplete="off" />
                </x-vuexy-admin::card.basic>

                {{-- Configuraciones --}}
                <x-vuexy-admin::card.basic title="Configuraciones">
                    <x-vuexy-admin::form.checkbox uid="random" model="status" label="Habilitar sucursal" switch="true" />
                    <x-vuexy-admin::form.checkbox uid="random" model="show_on_website" label="Mostrar en sitio web" switch="true" />
                    <x-vuexy-admin::form.checkbox uid="random" model="enable_ecommerce" label="eCommerce habilitado en sitio Web" switch="true" />
                </x-vuexy-admin::card.basic>
            </div>
            <div class="col-lg-4">
                {{-- Información de contacto --}}
                <x-vuexy-admin::card.basic title="Información de contacto">
                    <x-vuexy-admin::form.input type="tel" :uid="$uniqueId" model="tel" label="Teléfono" icon="ti ti-phone" phoneMode="national" />
                    <x-vuexy-admin::form.input type="tel" :uid="$uniqueId" model="tel2" label="Teléfono alternativo" icon="ti ti-phone" phoneMode="both" />
                    <x-vuexy-admin::form.input type="email" :uid="$uniqueId" model="email" label="Correo electrónico" icon="ti ti-mail" autocomplete="email" inputmode="email" />
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="manager_id" label="Gerente" :options="$manager_id_options" placeholder="Selecciona el gerente" />
                </x-vuexy-admin::card.basic>

                {{-- Información fiscal --}}
                <x-vuexy-admin::card.basic title="Información fiscal">
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="rfc" label="RFC" autocomplete="off" pattern="^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$" maxlength="13" />
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="nombre_fiscal" label="Nombre fiscal" autocomplete="organization" />
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="c_regimen_fiscal" label="Régimen fiscal" :options="$c_regimen_fiscal_options" placeholder="Selecciona el régimen fiscal" />
                    <x-vuexy-admin::form.input :uid="$uniqueId" model="domicilio_fiscal" label="Domicilio fiscal" autocomplete="address-line1" maxlength="100" />
                </x-vuexy-admin::card.basic>
            </div>
            <div class="col-lg-4">
                {{-- Dirección --}}
                <x-vuexy-contacts::card.address :uid="$uniqueId" :paisOptions="$c_pais_options" :estadoOptions="$c_estado_options" :localidadOptions="$c_localidad_options" :municipioOptions="$c_municipio_options" :coloniaOptions="$c_colonia_options"/>

                {{-- Ubicación --}}
                <x-vuexy-contacts::card.location :uid="$uniqueId" />
            </div>
        </div>
    </x-vuexy-admin::form>
</div>

@push('page-script')
    <script>
        const initializeStoreForm = (mode) => {
            const initializeContactInformation = () => {
                let $manager_id = $("#manager_id_{{ $uniqueId }}");

                $manager_id
                    .select2({
                        language: "es",
                        placeholder: "Selecciona el gerente",
                        allowClear: true,
                        width: "100%"
                    })
                    .on('select2:select select2:clear', function (e) {
                        @this.manager_id = e.params?.data?.id || null;
                    });
            }

            const initializeFiscalInformation = () => {
                let $c_regimen_fiscal = $("#c_regimen_fiscal_{{ $uniqueId }}");

                $c_regimen_fiscal
                    .select2({
                        language: "es",
                        placeholder: "Selecciona el regimen fiscal",
                        allowClear: true,
                        width: "100%"
                    })
                    .on('select2:select select2:clear', function (e) {
                        @this.c_regimen_fiscal = e.params?.data?.id || null;
                    });
            }

            const initializeLocationIQ = () => {
                //
            }

            const initializeAddressFormHandler = () => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Definición de selectores AddressFormHandler
                formSelectors = {
                    c_pais: '#c_pais_{{ $uniqueId }}',
                    c_estado: '#c_estado_{{ $uniqueId }}',
                    c_localidad: '#c_localidad_{{ $uniqueId }}',
                    c_municipio: '#c_municipio_{{ $uniqueId }}',
                    c_colonia: '#c_colonia_{{ $uniqueId }}',
                    c_codigo_postal: '#c_codigo_postal_{{ $uniqueId }}',
                    direccion: '#direccion_{{ $uniqueId }}',
                    notification: '#{{ $formId }} .address-notification'
                };

                // Definición de rutas AJAX Componente AddressFormHandler
                const ajaxRoutes = {
                    codigo_postal: "{{ route('admin.core.sat.get.ajax', 'codigo_postal') }}",
                    localidad: "{{ route('admin.core.sat.get.ajax', 'localidad') }}",
                    estado: "{{ route('admin.core.sat.get.ajax', 'estado') }}",
                    municipio: "{{ route('admin.core.sat.get.ajax', 'municipio') }}",
                    colonia: "{{ route('admin.core.sat.get.ajax', 'colonia') }}"
                };

                // Inicializamos el handler de la información de la dirección
                new AddressFormHandler(formSelectors, ajaxRoutes, @this, csrfToken);
            }

            const initializeLocationCard = (mode) => {
                const locationInputs = {
                    search: '#location_search_{{ $uniqueId }}',
                    btnSearch: '#btn_search_{{ $uniqueId }}',
                    lat: '#lat_{{ $uniqueId }}',
                    lng: '#lng_{{ $uniqueId }}',
                    btnClear: '#{{ $formId }} .btn-clear-coords',
                    mapId: 'locationMap_{{ $uniqueId }}',
                }

                leafletMap = LeafletMapHelper.initializeMap(locationInputs, mode, @this);
            }


            // Inicializamos Tarjeta de Información de contacto
            initializeContactInformation();

            // Inicializamos Tarjeta de Información fiscal
            initializeFiscalInformation();

            // Inicializamos Tarjeta de Dirección
            initializeAddressFormHandler();

            // Inicializamos Tarjeta de Ubicación
            initializeLocationCard(mode);

            // Deshabilitamos el formulario si estamos eliminando
            if (mode === 'delete') {
                window.disableStoreForm('#{{ $formId }}');
            }
        }

        // Evento para inicializar el formulario
        document.addEventListener("DOMContentLoaded", () => {
            document.addEventListener('on-failed-validation-store', (event) => {
                setTimeout(() => {
                    initializeStoreForm('{{ $mode }}');
                }, 10);
            });

            initializeStoreForm('{{ $mode }}');
        });
    </script>
@endpush
