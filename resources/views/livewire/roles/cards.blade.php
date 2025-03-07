<div>
    <p class="mb-4">Un rol proporciona acceso a menús y funciones predefinidas para que, según el rol asignado por un administrador, el usuario tener acceso a lo que necesite.</p>

    <!-- Role cards -->
    <div class="row g-4">
        @foreach($roles as $role)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-normal mb-2">Total {{ $role->users->count() }} usuario{{ $role->users->count() == 1? '': 's' }}</h6>
                            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                @foreach($role->users->take(10) as $user)
                                    <li data-bs-toggle="tooltip"
                                        data-popup="tooltip-custom"
                                        data-bs-placement="top"
                                        title="{{ $user->name }}"
                                        class="avatar avatar-sm pull-up">
                                        <img class="rounded-circle" src="{{ asset($user->profile_photo_url) }}" alt="Avatar" />
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <div class="role-heading">
                                <h4 class="mb-1 role-name">{{ $role->name }}</h4>
                                <span class="badge rounded-pill bg-label-{{ $role->style }}">Style: {{ $role->style }}</span>
                            </div>
                            <div>
                                <a  href="javascript:;" data-bs-toggle="modal" data-bs-target="#roleModal" wire:click="loadRoleData('view', {{ $role->id }})"
                                    class="text-body ms-2"
                                    data-bs-toggle="tooltip"
                                    data-popup="tooltip-custom"
                                    data-bs-placement="top"
                                    title="Ver rol">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('system.roles.edit')
                                    @if ($role->name != 'SuperAdmin' && $role->name != 'Admin')
                                        <a  href="javascript:;" data-bs-toggle="modal" data-bs-target="#roleModal" wire:click="loadRoleData('edit', {{ $role->id }})"
                                            class="text-body ms-2"
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Editar rol">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                @endcan
                                @can('system.roles.create')
                                    <a  href="javascript:;" data-bs-toggle="modal" data-bs-target="#roleModal" wire:click="loadRoleData('clone', {{ $role->id }})"
                                        class="text-body ms-2"
                                        data-bs-toggle="tooltip"
                                        data-popup="tooltip-custom"
                                        data-bs-placement="top"
                                        title="Crear una copia">
                                        <i class="fa-regular fa-copy"></i>
                                    </a>
                                @endcan
                                @can('system.roles.delete')
                                    @if ($role->name != 'SuperAdmin' && $role->name != 'Admin')
                                        <a  href="javascript:;" data-bs-toggle="modal" data-bs-target="#roleDeleteModal" data-id="{{ $role->id }}"
                                            class="role-delete-modal text-body ms-2"
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Eliminar">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @can('system.roles.create')
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card h-100">
                    <div class="row h-100">
                        <div class="col-sm-5">
                            <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
                                <img src="{{ asset('vendor/vuexy-admin/img/illustrations/add-new-roles.png') }}" class="img-fluid mt-sm-4 mt-md-0" alt="add-new-roles" width="83">
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="card-body text-sm-end text-center ps-sm-0">
                                <button data-bs-target="#roleModal" data-bs-toggle="modal" class="btn btn-primary mb-2 text-nowrap add-new-role">
                                    <i class="fa-solid fa-plus me-1"></i> Nuevo rol
                                </button>
                                <p class="mb-0 mt-1">Agregar rol, si no existe</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
    <!--/ Role cards -->

    <!-- Role Modals -->
    @include('vuexy-admin::roles._form_modal')
    @include('vuexy-admin::roles._delete_modal')
    <!-- / Role Modals -->
<div>

@push('page-script')
    <script>
        // Función para obtener elementos del formulario
        const getFormElements = () => {
            const form = document.getElementById('roleForm');
            const formElements = form.querySelectorAll('input, select, textarea, button');

            return {
                roleModal: document.getElementById('roleModal'),
                roleTitle: document.querySelector('.role-title'),
                form: form,
                inputId: document.querySelector('#roleForm input[name="id"]'),
                inputName: document.querySelector('#roleForm input[name="name"]'),
                formElements: formElements,
                addRoleButton: document.querySelector('.add-new-role'),
                selectAll: document.querySelector('#selectAll'),
                submitButton: document.querySelector('#roleForm button[type="submit"]'),
                deleteRoleButtons: document.querySelectorAll('.role-delete-modal'),
                deleteRoleModal: document.getElementById('roleDeleteModal'),
                deleteRoleForm: document.getElementById('deleteRoleForm'),
                deleteRoleText: document.querySelector('#roleDeleteModal .confirmation-text'),
            };
        };

        // Función para habilitar o deshabilitar el formulario
        const habilitarForm = (enableForm = true) => {
            const { formElements, submitButton } = getFormElements();

            formElements.forEach(element => {
                element.disabled = !enableForm;
            });

            submitButton.disabled = !enableForm;
        }

        // Función para resetear el formulario
        const resetForm = () => {
            const { roleTitle, form, submitButton } = getFormElements();

            roleTitle.textContent = 'Agregar un nuevo rol';
            submitButton.textContent = 'Crear nuevo rol';

            form.reset();

            // Limpiar errores de validación
            form.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });

            // Restablecer mensajes de error
            form.querySelectorAll('.invalid-feedback').forEach(element => {
                element.textContent = '';
            });
        };

        // Función para inicializar la validación del formulario
        const initializeFormValidation = (form) => {
            const { inputId, inputName } = getFormElements();

            FormValidation.formValidation(form, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: 'El nombre del rol es requerido'
                            },
                            // Agregar una regla personalizada para la validación AJAX
                            remote: {
                                url: '{{ route('admin.core.roles.check-unique-name') }}',
                                data: function() {
                                    return {
                                        name: inputName.value,
                                        id: inputId.value,
                                    };
                                },
                                message: 'Este nombre de rol ya está en uso',
                                method: 'GET'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.col-12'
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    autoFocus: new FormValidation.plugins.AutoFocus(),
                }
            })
            .on('core.form.valid', function(e) {
                Livewire.dispatch('saveRole');
            });
        };

        // Función para agregar el listener a un botón de eliminación de rol
        const addDeleteRoleListener = (button) =>  {
            button.addEventListener('click', function() {
                const { deleteRoleText } = getFormElements();

                var roleText = '¿Está seguro que desea eliminar el rol "' + button.closest('.card').querySelector('.role-name').innerHTML.trim() + '"?';

                @this.deleteRoleId = this.dataset.id;
                deleteRoleText.textContent = roleText;
            });
        }

        // Función para cambiar el estado de los checkboxes de permisos
        const setPermissionCheckboxState = (input, state) => {
            if(!input)
                return false;

            let modelName = input.getAttribute('wire:model');

            modelName = modelName.split('.').pop();

            if(modelName)
                @this.permissionsInputs[modelName] = state;
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('reloadForm', () => {
                setTimeout(() => {
                    const { form, selectAll, deleteRoleForm } = getFormElements();

                    // Seleccionar/deseleccionar todos los checkboxes
                    selectAll.addEventListener('change', e => {
                        Object.keys(@this.permissionsInputs).forEach(key => {
                            @this.permissionsInputs[key] = e.target.checked;
                        });
                    });

                    const checkboxList = document.querySelectorAll('#roleForm .permission-row input[type="checkbox"]');

                    checkboxList.forEach(checkbox => {
                        checkbox.addEventListener('change', e => {
                            let permissionType = e.target.dataset.type,
                            permissionsRow = e.target.closest('.permission-row');

                            if (permissionType == 'view' && !e.target.checked) {
                                let permissionCheckboxView   = permissionsRow.querySelector('input[data-type="view"]'),
                                    permissionCheckboxCreate  = permissionsRow.querySelector('input[data-type="create"]'),
                                    permissionCheckboxEdit  = permissionsRow.querySelector('input[data-type="edit"]'),
                                    permissionCheckboxCancel  = permissionsRow.querySelector('input[data-type="cancel"]'),
                                    permissionCheckboxDelete = permissionsRow.querySelector('input[data-type="delete"]');

                                if(permissionCheckboxView)
                                    setPermissionCheckboxState(permissionCheckboxView, false);

                                if(permissionCheckboxCreate)
                                    setPermissionCheckboxState(permissionCheckboxCreate, false);

                                if(permissionCheckboxEdit)
                                    setPermissionCheckboxState(permissionCheckboxEdit, false);

                                if(permissionCheckboxCancel)
                                    setPermissionCheckboxState(permissionCheckboxCancel, false);

                                if(permissionCheckboxDelete)
                                    setPermissionCheckboxState(permissionCheckboxDelete, false);
                            }

                            if ((permissionType == 'create' || permissionType == 'edit' || permissionType == 'cancel' || permissionType == 'delete') && e.target.checked) {
                                let permissionCheckboxView = permissionsRow.querySelector('input[data-type="view"]');

                                setPermissionCheckboxState(permissionCheckboxView, true);
                            }
                        });
                    });


                    // Validación de formulario
                    initializeFormValidation(form);

                    deleteRoleForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Livewire.dispatch('deleteRole');
                    });
                }, 1);
            });

            Livewire.on('habilitarFormulario', () => {
                habilitarForm();
            })

            Livewire.on('deshabilitarFormulario', () => {
                setTimeout(() => {
                    habilitarForm(false);
                }, 1);
            })

            Livewire.on('modalHide', () => {
                const { roleModal } = getFormElements();
                const modal = bootstrap.Modal.getInstance(roleModal);

                modal.hide();
            });

            Livewire.on('modalDeleteHide', () => {
                const { deleteRoleModal } = getFormElements();
                const modal = bootstrap.Modal.getInstance(deleteRoleModal);

                modal.hide();
            });

            Livewire.on('saveRole', () => {
                const { deleteRoleButtons } = getFormElements();

                deleteRoleButtons.forEach(button => {
                    addDeleteRoleListener(button);
                });
            });


            const { roleModal, addRoleButton, deleteRoleButtons } = getFormElements();

            // Al agregar Rol
            addRoleButton.addEventListener('click', function() {
                const { form } = getFormElements();

                resetForm();
                habilitarForm();
            });

            // Al abrir el Modal Crear / Editar / Clonar
            roleModal.addEventListener('shown.bs.modal', function () {
                const { inputName } = getFormElements();

                inputName.focus();
            });

            // Eliminar
            deleteRoleButtons.forEach(button => {
                addDeleteRoleListener(button);
            });

        });
    </script>
@endsection
