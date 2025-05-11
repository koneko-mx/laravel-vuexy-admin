<section id="admin-crm-contacts-view">
    <div class="row" x-data="{
        activeTab: 'general-info',
        activeTabPan: localStorage.getItem('activeTabPan') || 'cuenta-usuario',
        setActiveTabPan(tab) {
            this.activeTabPan = tab;
            localStorage.setItem('activeTabPan', tab);
        }
    }">

    <div class="col-md-12">
        <div class="nav-align-top">
            <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                <li class="nav-item">
                    <a @click="activeTab='general-info'" :class="{ 'active': activeTab === 'general-info' }" class="nav-link">Información general</a>
                </li>
                <li class="nav-item">
                    <a @click="activeTab='cotizaciones'" :class="{ 'active': activeTab === 'cotizaciones' }" x-on:click="loadTable('cotizaciones')" class="nav-link">Cotizaciones</a>
                </li>
                <li class="nav-item">
                    <a @click="activeTab='pago-pendiente'" :class="{ 'active': activeTab === 'pago-pendiente' }" x-on:click="loadTable('pago-pendiente')" class="nav-link">Pago pendiente</a>
                </li>
                <li class="nav-item">
                    <a @click="activeTab='pendientes-entrega'" :class="{ 'active': activeTab === 'pendientes-entrega' }" x-on:click="loadTable('pendientes-entrega')" class="nav-link">Pendientes de entrega</a>
                </li>
                <li class="nav-item">
                    <a @click="activeTab='procesados'" :class="{ 'active': activeTab === 'procesados' }" x-on:click="loadTable('procesados')" class="nav-link">Procesados</a>
                </li>
                <li class="nav-item">
                    <a @click="activeTab='pagos'" :class="{ 'active': activeTab === 'pagos' }" x-on:click="loadTable('pagos')" class="nav-link">Pagos</a>
                </li>
            </ul>
        </div>
        <div>
            <div x-show="activeTab === 'general-info'">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="nav-align-left nav-tabs-shadow mb-6">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button type="button" @click="setActiveTabPan('cuenta-usuario')" :class="{ 'active': activeTabPan === 'cuenta-usuario' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-cuenta-usuario" aria-controls="navs-left-cuenta-usuario" tabindex="-1">
                                        <i class="ti ti-user pr-2"></i> Cuenta de usuario
                                    </button>
                                </li>
                                @if (($is_customer) && $this->tipo_persona != Koneko\VuexyAdmin\Models\User::TIPO_RFC_PUBLICO)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" @click="setActiveTabPan('accesos')" :class="{ 'active': activeTabPan === 'accesos' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-accesos" aria-controls="navs-left-accesos">
                                            <i class="ti ti-key pr-2"></i> Accesos
                                        </button>
                                    </li>
                                @endif
                                @if ($is_prospect || $is_customer || $is_provider)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" @click="setActiveTabPan('facturacion-electronica')" :class="{ 'active': activeTabPan === 'facturacion-electronica' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-facturacion-electronica" aria-controls="navs-left-facturacion-electronica">
                                            <i class="ti ti-rubber-stamp pr-2"></i> Facturación electrónica
                                        </button>
                                    </li>
                                @endif
                                @if (($is_prospect || $is_customer || $is_provider) && $this->tipo_persona != Koneko\VuexyAdmin\Models\User::TIPO_RFC_PUBLICO)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" @click="setActiveTabPan('direcciones')" :class="{ 'active': activeTabPan === 'direcciones' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-direcciones" aria-controls="navs-left-direcciones">
                                            <i class="ti ti-map-pin pr-2"></i> Direcciones
                                        </button>
                                    </li>
                                @endif
                                @if (($is_prospect || $is_customer || $is_provider) && $this->tipo_persona != Koneko\VuexyAdmin\Models\User::TIPO_RFC_PUBLICO)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" @click="setActiveTabPan('contacto')" :class="{ 'active': activeTabPan === 'contacto' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-contacto" aria-controls="navs-left-contacto">
                                            <i class="ti ti-address-book pr-2"></i> Contacto
                                        </button>
                                    </li>
                                @endif
                                @if (($is_customer || $is_provider) && $this->tipo_persona != Koneko\VuexyAdmin\Models\User::TIPO_RFC_PUBLICO)
                                    <li class="nav-item" role="presentation">
                                        <button type="button" @click="setActiveTabPan('cuentas-bancarias')" :class="{ 'active': activeTabPan === 'cuentas-bancarias' }" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-cuentas-bancarias" aria-controls="navs-left-cuentas-bancarias">
                                            <i class="ti ti-credit-card pr-2"></i> Cuentas bancarias
                                        </button>
                                    </li>
                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'cuenta-usuario' }" id="navs-left-cuenta-usuario" role="tabpanel">
                                    @if($cuentaUsuarioAlert)
                                        <div class="alert alert-{{ $cuentaUsuarioAlert['type'] }} alert-cuenta-usuario alert-dismissible" role="alert">
                                            {{ $cuentaUsuarioAlert['message'] }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <form id="cuentaUsuario-form" class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                        <input type="hidden" name="userId" wire:model='userId'>

                                        <div class="row">
                                            <div class="col-lg-6" wire:ignore>
                                                <div class="row custom-options-checkable mb-4">
                                                    <div class="col-md-4">
                                                        <input class="custom-option-item-check" id="tipo_persona_fisica" name="tipo_persona" type="radio" value="1" wire:model="tipo_persona" >
                                                        <label class="custom-option-item p-3" for="tipo_persona_fisica">
                                                            <span class="d-flex justify-content-between flex-wrap">
                                                                <span class="fw-bolder">Persona física</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input class="custom-option-item-check" id="tipo_persona_moral" name="tipo_persona" type="radio" value="2" wire:model="tipo_persona">
                                                        <label class="custom-option-item p-3" for="tipo_persona_moral">
                                                            <span class="d-flex justify-content-between flex-wrap">
                                                                <span class="fw-bolder">Persona moral</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input class="custom-option-item-check" id="tipo_publico_en_general" name="tipo_persona" type="radio" value="9" wire:model="tipo_persona">
                                                        <label class="custom-option-item p-3" for="tipo_publico_en_general">
                                                            <span class="d-flex justify-content-between flex-wrap">
                                                                <span class="fw-bolder">Público en general</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="name" class="form-label">Nombre</label>
                                                    <div class="input-group input-group-merge input-group-name">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-building-skyscraper"></i>
                                                            <i class="ti ti-user"></i>
                                                        </span>
                                                        <input type="text" id="name" name="name" wire:model='name' class="form-control" placeholder="Nombre completo">
                                                    </div>
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="fv-row cargo-div mb-6">
                                                    <label for="cargo" class="form-label">Cargo o puesto laboral</label>
                                                    <input type="text" id="cargo" name="cargo" wire:model='cargo' class="form-control" placeholder="Cargo o puesto laboral">
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <x-checkbox-v value="{{ old('is_prospect', $is_prospect) }}"
                                                                    name='is_prospect'
                                                                    wire:model='is_prospect'
                                                                    parent_class='form-switch'>
                                                        Es prospecto
                                                    </x-checkbox-v>
                                                </div>
                                                <div class="mb-3">
                                                    <x-checkbox-v value="{{ old('is_customer', $is_customer) }}"
                                                                    name='is_customer'
                                                                    wire:model='is_customer'
                                                                    parent_class='form-switch'>
                                                        Es cliente
                                                    </x-checkbox-v>
                                                </div>
                                                <div class="mb-3">
                                                    <x-checkbox-v value="{{ old('is_provider', $is_provider) }}"
                                                                    name='is_provider'
                                                                    wire:model='is_provider'
                                                                    parent_class='form-switch'>
                                                        Es proveedor
                                                    </x-checkbox-v>
                                                </div>
                                                <div class="row pricelist-div mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="pricelist_id" class="form-label">Lista de precios</label>
                                                        <x-form.select name="pricelist_id" wire:model='pricelist_id' :options="$pricelists_options" :placeholder="'Selecciona la lista de precios'" />
                                                    </div>
                                                </div>
                                                <div class="row credit-div">
                                                    <div class="col-md-6 mt-3 mb-3">
                                                        <x-checkbox-v value="{{ old('enable_credit', $enable_credit) }}"
                                                                    name='enable_credit'
                                                                    wire:model='enable_credit'
                                                                    parent_class='form-switch'>
                                                            Habilitar línea de crédito
                                                        </x-checkbox-v>
                                                    </div>
                                                </div>
                                                <div class="row credit-div credit-limit-div">
                                                    <div class="col-md-6 fv-row ">
                                                        <label for="credit_days" class="form-label">Máximo de días de crédito</label>
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ti ti-calendar-event"></i></span>
                                                            <input type="number" id="credit_days" name="credit_days" wire:model='credit_days' class="form-control text-center">
                                                        </div>
                                                        <div class="error-message"></div>
                                                    </div>
                                                    <div class="col-md-6 fv-row credit-limit-div">
                                                        <label for="credit_limit" class="form-label">Línea de cŕedito</label>
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" id="credit_limit" name="credit_limit" wire:model='credit_limit' class="form-control text-right">
                                                        </div>
                                                        <div class="error-message"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="image-wrapper-520x520 mb-3">
                                                    <!-- Mostrar una vista previa de la imagen seleccionada -->
                                                    <img id="user-image" src="{{ $image? $image->temporaryUrl() : $profile_photo }}">
                                                </div>
                                                <input type="file" name="image" wire:model="image" id="image" class='form-control' accept='image/*' />
                                                @error('image') <span class="error">{{ $message }}</span> @enderror
                                                @if(!$deleteUserImage)
                                                    <button type="button" class="btn btn-outline-warning waves-effect btn-delete-image mt-3" onclick="btnDeleteImage()">Eliminar imagen</button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mt-2" wire:ignore>
                                            <div class="col-lg-12">
                                                <button type="submit" id="cuentaUsuario-saveButton" class="btn btn-primary me-3 waves-effect waves-light" disabled>Guardar cambios</button>
                                                <button type="button" id="cuentaUsuario-resetButton" class="btn btn-label-secondary waves-effect" disabled>Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'accesos' }" id="navs-left-accesos" role="tabpanel">
                                    @if($accesosAlert)
                                        <div class="alert alert-{{ $accesosAlert['type'] }} alert-accesos alert-dismissible" role="alert">
                                            {{ $accesosAlert['message'] }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <form id="accesos-form" class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="fv-row mb-3">
                                                    <label for="status" class="form-label">Estatus</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-shield-exclamation"></i></span>
                                                        <x-form.select name="status" :options="$status_options" wire:model='status'/>
                                                    </div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="email" class="form-label">Correo electrónico</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                                        <input type="text" id="email" name="name" wire:model='email' class="form-control" placeholder="Correo electrónico">
                                                    </div>
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="password" class="form-label">Contraseña</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-key"></i></span>
                                                        <input type="password" id="password" name="password" wire:model='password' class="form-control" placeholder="Dejar en blanco para no cambiar">
                                                    </div>
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-key"></i></span>
                                                        <input type="password" id="password_confirmation" name="password_confirmation"  wire:model='password_confirmation' class="form-control" placeholder="Dejar en blanco para no cambiar">
                                                    </div>
                                                    <div class="error-message"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2" wire:ignore>
                                            <div class="col-lg-12">
                                                <button type="submit" id="accesos-saveButton" class="btn btn-primary me-3 waves-effect waves-light" disabled>Guardar cambios</button>
                                                <button type="button" id="accesos-resetButton" class="btn btn-label-secondary waves-effect" disabled>Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'facturacion-electronica' }" id="navs-left-facturacion-electronica" role="tabpanel">
                                    @if($facturacionElectronicaAlert)
                                        <div class="alert alert-{{ $facturacionElectronicaAlert['type'] }} alert-facturacion-electronica alert-dismissible" role="alert">
                                            {{ $facturacionElectronicaAlert['message'] }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <form id="facturacion-electronica-form" class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-lg-6 fv-row mb-3">
                                                        <label for="rfc" class="form-label">RFC</label>
                                                        <input type="text" id="rfc" name="name" wire:model='rfc' class="form-control uppercase" placeholder="RFC">
                                                        <div class="error-message"></div>
                                                    </div>
                                                    <div class="col-lg-6 fv-row mb-3">
                                                        <label for="domicilio_fiscal" class="form-label">Domicilio fiscal (C.P.)</label>
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ti ti-mailbox"></i></span>
                                                            <input type="number" id="domicilio_fiscal" name="domicilio_fiscal" wire:model='domicilio_fiscal' class="form-control text-center" placeholder="Código postal">
                                                        </div>
                                                        <div class="error-message"></div>
                                                    </div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="nombre_fiscal" class="form-label">Nombre fiscal</label>
                                                    <input type="text" id="nombre_fiscal" name="nombre_fiscal" wire:model='nombre_fiscal' class="form-control uppercase" placeholder="Nombre fiscal">
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="c_regimen_fiscal" class="form-label">Regimen fiscal</label>
                                                    <x-form.select name="c_regimen_fiscal" :options="$regimen_fiscal_options" wire:model='c_regimen_fiscal' :placeholder="'Selecciona regimen fiscal'" />
                                                    <div class="error-message"></div>
                                                </div>
                                                <div class="fv-row mb-3">
                                                    <label for="c_uso_cfdi" class="form-label">Uso de CFDI preferido</label>
                                                    <x-form.select name="c_uso_cfdi" :options="$uso_cfdi_options" wire:model='c_uso_cfdi' :placeholder="'Selecciona uso de CFDI'" />
                                                    <div class="error-message"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" wire:ignore>
                                                <div class="pdf-dropzone-div mb-4">
                                                    <div class="dropzone mini-dropzone" id="pdf-dropzone">
                                                        <div class="dz-message needsclick">
                                                            Constancia de situación fiscal
                                                            <span class="note needsclick">Arastre aquí el PDF o haga clic para cargarlo.</span>
                                                        </div>
                                                    </div>
                                                    <div class="error-message"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2" wire:ignore>
                                            <div class="col-lg-12">
                                                <button type="submit" id="facturacion-electronica-saveButton" class="btn btn-primary me-3 waves-effect waves-light" disabled>Guardar cambios</button>
                                                <button type="button" id="facturacion-electronica-resetButton" class="btn btn-label-secondary waves-effect" disabled>Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'direcciones' }" id="navs-left-direcciones" role="tabpanel">
                                    <p>Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies cupcake gummi</p>
                                </div>
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'contacto' }" id="navs-left-contacto" role="tabpanel">
                                    <p>Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies cupcake gummi</p>
                                </div>
                                <div class="tab-pane fade" :class="{ 'active show': activeTabPan === 'cuentas-bancarias' }" id="navs-left-cuentas-bancarias" role="tabpanel">
                                    <p>Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies cupcake gummi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 'cotizaciones'">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="dt-cotizaciones table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 'pago-pendiente'">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="dt-pago-pendiente table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 'pendientes-entrega'">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="dt-pendientes-entrega table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 'procesados'">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="dt-procesados table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div x-show="activeTab === 'pagos'">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="dt-pagos table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@push('page-script')
    <script>
        const cuentaUsuarioSaveButton  = document.getElementById('cuentaUsuario-saveButton');
        const cuentaUsuarioResetButton = document.getElementById('cuentaUsuario-resetButton');
        const accesosSaveButton  = document.getElementById('accesos-saveButton');
        const accesosResetButton = document.getElementById('accesos-resetButton');
        const facturacionElectronicaSaveButton  = document.getElementById('facturacion-electronica-saveButton');
        const facturacionElectronicaResetButton = document.getElementById('facturacion-electronica-resetButton');

        const btnDeleteImage = () => {
            $("#user-image").prop('src', '');

            cuentaUsuarioSaveButton.disabled  = false;
            cuentaUsuarioResetButton.disabled = false;

            @this.set('image', null, false);
            @this.set('deleteUserImage', true, false);

            $('.btn-delete-image').remove();
        }

        const initGeneralInfo = () => {
            $("input[name=tipo_persona]")
                .on("change", function () {
                    configInputsGeneralInfo();
                });

            $("#is_prospect")
                .on('change', function(){
                    if($("#is_prospect").is(':checked') && $("#is_customer").is(":checked")){
                        @this.set('is_customer', false, false).then(() => {
                            configInputsGeneralInfo();
                        });

                    }else{
                        configInputsGeneralInfo();
                    }
                });

            $("#is_customer")
                .on('change', function(){
                    if($("#is_prospect").is(':checked') && $("#is_customer").is(":checked")){
                        @this.set('is_prospect', false, false).then(() => {
                            configInputsGeneralInfo();
                        });

                    }else{
                        configInputsGeneralInfo();
                    }
                });

            $("#enable_credit")
                .on('change', function(){
                    configInputsGeneralInfo();
                });

            $("#image")
                .on('change', function(){
                    @this.set('cuentaUsuarioAlert', null, false);
                    @this.set('deleteUserImage', false, false);
                });

            $('#cuentaUsuario-form :input')
                .on('input', function(){
                    cuentaUsuarioSaveButton.disabled  = false;
                    cuentaUsuarioResetButton.disabled = false;

                    @this.set('cuentaUsuarioAlert', null, false).then(() => {
                        $('.alert-cuenta-usuario').remove();
                    });
                });

            $('#cuentaUsuario-resetButton')
                .on('click', function () {
                    @this.reloadUserData();

                    cuentaUsuarioSaveButton.disabled  = true;
                    cuentaUsuarioResetButton.disabled = true;
                });

            $("#cuentaUsuario-form")
                .validate({
                    errorClass: 'error',
                    highlight: function(element, errorClass, validClass) {
                        // Agrega la clase de error a la fila (contenedor del campo)
                        $(element).closest('.fv-row').addClass('has-error');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        // Elimina la clase de error de la fila (contenedor del campo)
                        $(element).closest('.fv-row').removeClass('has-error');
                    },
                    errorPlacement: function(error, element) {
                        // Controla dónde se colocan los mensajes de error
                        error.appendTo(element.closest('.fv-row').find('.error-message'));
                    },
                    rules: {
                        name: {
                            required: true,
                            minlength: 3,
                            maxlength: 255,
                            normalizer: function(value) {
                                return $.trim(value); // Aplicar trim antes de validar
                            }
                        },
                        cargo: {
                            minlength: 3,
                            maxlength: 255,
                            normalizer: function(value) {
                                return $.trim(value); // Aplicar trim antes de validar
                            }
                        }
                    },
                    messages: {
                        name: {
                            required: "Por favor ingrese el nombre del contacto",
                        },
                    },
                    submitHandler: function(form, event) {
                        event.preventDefault();

                        @this.call('saveCuentaUsuario').then(() => {
                            initAllForms();

                            const cuentaUsuarioSaveButton = document.getElementById('cuentaUsuario-saveButton');
                            const cuentaUsuarioResetButton = document.getElementById('cuentaUsuario-resetButton');

                            cuentaUsuarioSaveButton.disabled  = true;
                            cuentaUsuarioResetButton.disabled = true;
                        });
                    }
                });
        };

        const configInputsGeneralInfo = () => {
            let is_prospect = $("#is_prospect").is(":checked"),
                is_customer = $("#is_customer").is(":checked"),
                enable_credit = $("#enable_credit").is(":checked"),
                tipo_persona = Number($("input[name=tipo_persona]:checked").val());

            // Persona física
            if(tipo_persona == 1){
                $(".cargo-div").show();

            }else{
                $(".cargo-div").hide();
            }

            // Persona moral
            if(tipo_persona == 2){
                $(".input-group-name i.ti-building-skyscraper").show();
                $(".input-group-name i.ti-user").hide();

            }else{
                $(".input-group-name i.ti-building-skyscraper").hide();
                $(".input-group-name i.ti-user").show();
            }

            // Publico en general
            if(tipo_persona == 9){
                @this.set('is_prospect', false, false);
                @this.set('is_customer', true, false);
                @this.set('is_provider', false, false);
                @this.set('enable_credit', false, false);

                $("#is_prospect").prop("disabled", true);
                $("#is_provider").prop("disabled", true);
            }else{
                $("#is_prospect").prop("disabled", false);
                $("#is_provider").prop("disabled", false);
            }

            if (is_prospect || is_customer) {
                $('.pricelist-div').show();

            }else{
                $('.pricelist-div').hide();
            }

            if (is_customer && tipo_persona != 9) {
                $('.credit-div').show();

                if (enable_credit)
                    $('.credit-limit-div').show();

                else
                    $('.credit-limit-div').hide();
            }else{
                $('.credit-div').hide();
                $('.credit-limit-div').hide();
            }
        }

        const initAccesses = () => {
            $('#accesos-form :input')
                .on('input', function(){
                    accesosSaveButton.disabled  = false;
                    accesosResetButton.disabled = false;

                    @this.set('accesosAlert', null, false).then(() => {
                        $('.alert-accesos').remove();
                    });
                });

            $('#accesos-resetButton')
                .on('click', function () {
                    @this.reloadUserData();

                    accesosSaveButton.disabled  = true;
                    accesosResetButton.disabled = true;
                });

            $("#accesos-form")
                .validate({
                    errorClass: 'error',
                    highlight: function(element, errorClass, validClass) {
                        // Agrega la clase de error a la fila (contenedor del campo)
                        $(element).closest('.fv-row').addClass('has-error');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        // Elimina la clase de error de la fila (contenedor del campo)
                        $(element).closest('.fv-row').removeClass('has-error');
                    },
                    errorPlacement: function(error, element) {
                        // Controla dónde se colocan los mensajes de error
                        error.appendTo(element.closest('.fv-row').find('.error-message'));
                    },
                    rules: {
                        email: {
                            required: true,
                            email: true,
                        },
                        password: {
                            minlength: 5,
                            maxlength: 32,
                        },
                        password_confirmation: {
                            equalTo: "#password" // Valida que coincida con el campo 'password'
                        }
                    },
                    messages: {
                        email: {
                            required: "El correo electrónico es obligatorio.",
                            email: "Debes ingresar un correo electrónico válido.",
                        },
                        password: {
                            minlength: "La contraseña debe tener al menos 5 caracteres.",
                            maxlength: "La contraseña no puede tener más de 32 caracteres."
                        },
                        password_confirmation: {
                            equalTo: "Las contraseñas no coinciden."
                        }
                    },
                    submitHandler: function(form, event) {
                        event.preventDefault();

                        @this.call('saveAccesos').then(() => {
                            initAllForms();

                            const accesosSaveButton = document.getElementById('accesos-saveButton');
                            const accesosResetButton = document.getElementById('accesos-resetButton');

                            accesosSaveButton.disabled  = true;
                            accesosResetButton.disabled = true;
                        });
                    }
                });
        }


        const initFiscalInfo = () => {
            $('#facturacion-electronica-form :input')
                .on('input', function(){
                    facturacionElectronicaSaveButton.disabled  = false;
                    facturacionElectronicaResetButton.disabled = false;

                    @this.set('facturacionElectronicaAlert', null, false).then(() => {
                        $('.alert-facturacion-electronica').remove();
                    });
                });

            $('#facturacion-electronica-resetButton')
                .on('click', function () {
                    @this.reloadUserData();

                    facturacionElectronicaSaveButton.disabled  = true;
                    facturacionElectronicaResetButton.disabled = true;
                });

            $("#facturacion-electronica-form")
                .validate({
                    errorClass: 'error',
                    highlight: function(element, errorClass, validClass) {
                        // Agrega la clase de error a la fila (contenedor del campo)
                        $(element).closest('.fv-row').addClass('has-error');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        // Elimina la clase de error de la fila (contenedor del campo)
                        $(element).closest('.fv-row').removeClass('has-error');
                    },
                    errorPlacement: function(error, element) {
                        // Controla dónde se colocan los mensajes de error
                        error.appendTo(element.closest('.fv-row').find('.error-message'));
                    },
                    rules: {
                        rfc: {
                            maxlength: 13,
                            pattern: /^[A-Z&Ñ]{3,4}\d{6}[A-Z\d]{3}$/, // Expresión regular para RFC
                            normalizer: function(value) {
                                return $.trim(value);
                            }
                        },
                        domicilio_fiscal: {
                            digits: true, // Solo permite números
                            minlength: 5,
                            maxlength: 5, // Debe ser exactamente de 5 dígitos
                        }
                    },
                    messages: {
                        rfc: {
                            maxlength: "El RFC no debe exceder los 13 caracteres.",
                            pattern: "El RFC ingresado no es válido. Verifica el formato.",
                        },
                        domicilio_fiscal: {
                            digits: "El código postal solo debe contener números.",
                            minlength: "El código postal debe tener 5 dígitos.",
                            maxlength: "El código postal debe tener 5 dígitos."
                        }
                    },
                    submitHandler: function(form, event) {
                        event.preventDefault();

                        @this.call('saveFacturacionElectronica').then(() => {
                            initAllForms();

                            const facturacionElectronicaSaveButton = document.getElementById('facturacion-electronica-saveButton');
                            const facturacionElectronicaResetButton = document.getElementById('facturacion-electronica-resetButton');

                            facturacionElectronicaSaveButton.disabled  = true;
                            facturacionElectronicaResetButton.disabled = true;
                        });
                    }
                });

        }


        const initAllForms = () => {
            initGeneralInfo();
            initAccesses();
            initFiscalInfo();
            configInputsGeneralInfo();
        }


        // Almacena un indicador para saber si las tablas ya han sido inicializadas
        let tablesInitialized = {
            'cotizaciones': false,
            'pago-pendiente': false,
            'pendientes-entrega': false,
            'procesados': false,
            'pagos': false
        };

        // Función para cargar una tabla específica si aún no ha sido inicializada
        function loadTable(tabName) {
            if (!tablesInitialized[tabName]) {
                switch (tabName) {
                    case 'cotizaciones':
                        initializeCotizacionesTable();
                        break;

                    case 'pago-pendiente':
                        initializePagoPendienteTable();
                        break;

                    case 'pendientes-entrega':
                        initializePendientesEntregaTable();
                        break;

                    case 'procesados':
                        initializeProcesadosTable();
                        break;

                    case 'pagos':
                        initializePagosTable();
                        break;
                }
                tablesInitialized[tabName] = true;
            }
        }

        function initializeCotizacionesTable() {
            var dt_cotizaciones_table = $('.dt-cotizaciones');

            if (dt_cotizaciones_table.length) {
                dt_cotizaciones = dt_cotizaciones_table.DataTable({
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            searchable: false,
                            visible: false
                        },
                        {
                            // Avatar image/badge, Name and post
                            targets: 2,
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {
                                var $user_img = full['avatar'],
                                $name = full['full_name'],
                                $post = full['post'];
                                if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + assetsPath + 'img/avatars/' + $user_img + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6);
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                                }
                                // Creates full output for row
                                var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $post +
                                '</small>' +
                                '</div>' +
                                '</div>';
                                return $row_output;
                            }
                        },
                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['status'];
                                var $status = {
                                1: { title: 'Current', class: 'bg-label-primary' },
                                2: { title: 'Professional', class: ' bg-label-success' },
                                3: { title: 'Rejected', class: ' bg-label-danger' },
                                4: { title: 'Resigned', class: ' bg-label-warning' },
                                5: { title: 'Applied', class: ' bg-label-info' }
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                return data;
                                }
                                return (
                                '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    displayLength: 5,
                    lengthMenu: [5, 10, 25, 50, 75, 100],
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    responsive: {
                        details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                        }
                    },
                    /*
                    initComplete: function (settings, json) {
                        $('.card-header').after('<hr class="my-0">');
                    }
                    */
                });
            }
        }

        function initializePagoPendienteTable() {
            var dt_pago_pendiente_table = $('.dt-pago-pendiente');

            if (dt_pago_pendiente_table.length) {
                dt_pago_pendiente = dt_pago_pendiente_table.DataTable({
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            searchable: false,
                            visible: false
                        },
                        {
                            // Avatar image/badge, Name and post
                            targets: 2,
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {
                                var $user_img = full['avatar'],
                                $name = full['full_name'],
                                $post = full['post'];
                                if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + assetsPath + 'img/avatars/' + $user_img + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6);
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                                }
                                // Creates full output for row
                                var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $post +
                                '</small>' +
                                '</div>' +
                                '</div>';
                                return $row_output;
                            }
                        },
                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['status'];
                                var $status = {
                                1: { title: 'Current', class: 'bg-label-primary' },
                                2: { title: 'Professional', class: ' bg-label-success' },
                                3: { title: 'Rejected', class: ' bg-label-danger' },
                                4: { title: 'Resigned', class: ' bg-label-warning' },
                                5: { title: 'Applied', class: ' bg-label-info' }
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                return data;
                                }
                                return (
                                '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    displayLength: 5,
                    lengthMenu: [5, 10, 25, 50, 75, 100],
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    responsive: {
                        details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                        }
                    },
                    /*
                    initComplete: function (settings, json) {
                        $('.card-header').after('<hr class="my-0">');
                    }
                    */
                });
            }
        }

        function initializePendientesEntregaTable() {
            var dt_pendientes_entrega_table = $('.dt-pendientes-entrega');

            if (dt_pendientes_entrega_table.length) {
                dt_pendientes_entrega = dt_pendientes_entrega_table.DataTable({
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            searchable: false,
                            visible: false
                        },
                        {
                            // Avatar image/badge, Name and post
                            targets: 2,
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {
                                var $user_img = full['avatar'],
                                $name = full['full_name'],
                                $post = full['post'];
                                if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + assetsPath + 'img/avatars/' + $user_img + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6);
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                                }
                                // Creates full output for row
                                var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $post +
                                '</small>' +
                                '</div>' +
                                '</div>';
                                return $row_output;
                            }
                        },
                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['status'];
                                var $status = {
                                1: { title: 'Current', class: 'bg-label-primary' },
                                2: { title: 'Professional', class: ' bg-label-success' },
                                3: { title: 'Rejected', class: ' bg-label-danger' },
                                4: { title: 'Resigned', class: ' bg-label-warning' },
                                5: { title: 'Applied', class: ' bg-label-info' }
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                return data;
                                }
                                return (
                                '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    displayLength: 5,
                    lengthMenu: [5, 10, 25, 50, 75, 100],
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    responsive: {
                        details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                        }
                    },
                    /*
                    initComplete: function (settings, json) {
                        $('.card-header').after('<hr class="my-0">');
                    }
                    */
                });
            }
        }

        function initializeProcesadosTable() {
            var dt_procesados_table = $('.dt-procesados');

            if (dt_procesados_table.length) {
                dt_procesados = dt_procesados_table.DataTable({
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            searchable: false,
                            visible: false
                        },
                        {
                            // Avatar image/badge, Name and post
                            targets: 2,
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {
                                var $user_img = full['avatar'],
                                $name = full['full_name'],
                                $post = full['post'];
                                if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + assetsPath + 'img/avatars/' + $user_img + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6);
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                                }
                                // Creates full output for row
                                var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $post +
                                '</small>' +
                                '</div>' +
                                '</div>';
                                return $row_output;
                            }
                        },
                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['status'];
                                var $status = {
                                1: { title: 'Current', class: 'bg-label-primary' },
                                2: { title: 'Professional', class: ' bg-label-success' },
                                3: { title: 'Rejected', class: ' bg-label-danger' },
                                4: { title: 'Resigned', class: ' bg-label-warning' },
                                5: { title: 'Applied', class: ' bg-label-info' }
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                return data;
                                }
                                return (
                                '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    displayLength: 5,
                    lengthMenu: [5, 10, 25, 50, 75, 100],
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    responsive: {
                        details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                        }
                    },
                    /*
                    initComplete: function (settings, json) {
                        $('.card-header').after('<hr class="my-0">');
                    }
                    */
                });
            }
        }

        function initializePagosTable() {
            var dt_pagos_table = $('.dt-pagos');

            if (dt_pagos_table.length) {
                dt_pagos = dt_pagos_table.DataTable({
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            searchable: false,
                            visible: false
                        },
                        {
                            // Avatar image/badge, Name and post
                            targets: 2,
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {
                                var $user_img = full['avatar'],
                                $name = full['full_name'],
                                $post = full['post'];
                                if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + assetsPath + 'img/avatars/' + $user_img + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6);
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                                }
                                // Creates full output for row
                                var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $post +
                                '</small>' +
                                '</div>' +
                                '</div>';
                                return $row_output;
                            }
                        },
                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['status'];
                                var $status = {
                                1: { title: 'Current', class: 'bg-label-primary' },
                                2: { title: 'Professional', class: ' bg-label-success' },
                                3: { title: 'Rejected', class: ' bg-label-danger' },
                                4: { title: 'Resigned', class: ' bg-label-warning' },
                                5: { title: 'Applied', class: ' bg-label-info' }
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                return data;
                                }
                                return (
                                '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    displayLength: 5,
                    lengthMenu: [5, 10, 25, 50, 75, 100],
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    responsive: {
                        details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['full_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                        }
                    },
                    /*
                    initComplete: function (settings, json) {
                        $('.card-header').after('<hr class="my-0">');
                    }
                    */
                });
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $("#pdf-dropzone")
                    .dropzone({
                        url: '{{ route('admin.crm.contacts.extract-data-pdf-certificate') }}',
                        paramName: "file",
                        maxFiles: 1,
                        acceptedFiles: '.pdf',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(file, response) {
                            if($('#name').val().trim() == '')
                                @this.set('name', response.nombre_fiscal, false);

                            @this.set('rfc', response.rfc, false);
                            @this.set('nombre_fiscal', response.nombre_fiscal, false);

                            if(response.c_regimen_fiscal)
                                @this.set('c_regimen_fiscal', response.c_regimen_fiscal, false);

                            @this.set('domicilio_fiscal', response.domicilio_fiscal, false);

                            facturacionElectronicaSaveButton.disabled  = false;
                            facturacionElectronicaResetButton.disabled = false;

                            @this.set('facturacionElectronicaAlert', null, false).then(() => {
                                $('.alert-facturacion-electronica').remove();
                            });

                            $('.pdf-dropzone-div').slideUp(200);
                        },
                        error: function(file, response) {
                            $('.pdf-dropzone-div .error-message').html('<label class="error">' + response + '</label>');

                            this.removeAllFiles(true);
                        }
                    });

                initAllForms();
            });
        });
    </script>
@endpush
