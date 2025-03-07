<div>
    <div class="users-index alert-errors">{!! $indexAlert !!}</div>

    <!-- Users List Table -->
    <div class="card" wire:ignore>
        <div class="card-datatable table-responsive">
            <table class="datatables-users table">
                <thead class="border-top">
                    <tr>
                        <th></th>
                        <th>Id</th>
                        <th>Usuario</th>
                        <th>Roles</th>
                        <th>Estatus</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Offcanvas to add new user -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUser" aria-labelledby="offcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasLabel" class="offcanvas-title">{{ $modalTitle }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form class="pt-0" id="userForm" autocomplete='off'>
                <input type="hidden" name="id" wire:model='userId' />

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre completo</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                        <input type="text" name="name" wire:model='name' id="name" class="form-control" placeholder="Pepe Pecas" />
                    </div>
                    <div class="error-message"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                        <input type="text" name="email" wire:model='email' id="email" class="form-control" placeholder="picapapas@mail.com" />
                    </div>
                    <div class="error-message"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group input-group-merge form-password-toggle">
                        <span class="input-group-text"><i class="ti ti-key"></i></span>
                        <input type="password" name="password" wire:model='password' class="form-control form-control-merge" id="password" placeholder="············" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye"></i></span>
                    </div>
                    <div class="error-message"></div>
                </div>
                <div class="mb-3">
                    <label for="roles" class="form-label">Roles del usuario</label>
                    <x-vuexy-admin::form.select
                        id="roles"
                        name="roles[]"
                        wire:model='roles'
                        :options="$roles_options"
                        multiple
                        class="select2 form-select" />
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Estatus</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-alert-triangle"></i></span>
                        <x-vuexy-admin::form.select
                            id="status"
                            name="status"
                            wire:model='status'
                            :options="$status_options"
                            class="form-select" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Imagen de perfil</label>
                    <div class="image-wrapper mb-1">
                        <img id="user-image" class="max-w-full" src="{{ $src_photo }}" alt="">
                    </div>
                    <input type="file" name="photo" id="photo" class='form-control' accept='image/*' />
                </div>

                <div class="alert-errors"></div>

                <button type="submit" class="btn btn-primary me-3 data-submit">{{ $btnSubmitTxt }}</button>
                <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Eliminar usuario</h3>
                        <p class="text-muted">El proceso de eliminación es definitivo e irreversible</p>
                    </div>
                    <form class="row g-3">
                        <div class="col-12">
                            <p class="name text-center font-bold"></p>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 btn-submit">Eliminar usuario</button>
                            <button type="reset" class="btn btn-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ Delete User Modal -->
</div>

@push('page-script')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const store_route = '{{ route('admin.core.users.store') }}',
                update_route  = '{{ route('admin.core.users.update-ajax', '0~0') }}',
                show_route    = '{{ route('admin.core.users.show', '0~0') }}',
                destroy_route = '{{ route('admin.core.users.destroy', '0~0') }}';

            var statusObj = <?= json_encode($statuses) ?>,
                $usersIndexAlert = $('.users-index.alert-errors'),
                $dt_user_table = $('.datatables-users'),
                dt_user;

            var offcanvasElement = document.getElementById('offcanvasUser'),
                offcanvasUser = new bootstrap.Offcanvas(offcanvasElement);


            load_js_form = () => {
                $('#userForm .select2')
                    .each(function() {
                        var $this = $(this)

                        $this.wrap('<div class="position-relative"></div>')

                        $this.select2({
                            dropdownAutoWidth: true,
                            width: '100%',
                            dropdownParent: $this.parent()
                        });
                    });


                // Previo de imagenes
                document.getElementById("photo").addEventListener('change', updatePreviewImage);


                // Reset form
                $("#userForm")
                    .on('reset', function(){
                        setTimeout(function(){
                            $('#roles').trigger('change');
                        }, 250)

                        $('#user-image').prop("src", "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");

                        $('#userForm .alert-errors').html('');
                    });


                $("#userForm")
                    .validate({
                        errorClass: 'error',
                        highlight: function(element, errorClass, validClass) {
                            // Agrega la clase de error a la fila (contenedor del campo)
                            $(element).closest('.mb-3').addClass('has-error');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            // Elimina la clase de error de la fila (contenedor del campo)
                            $(element).closest('.mb-3').removeClass('has-error');
                        },
                        errorPlacement: function(error, element) {
                            // Controla dónde se colocan los mensajes de error
                            error.appendTo(element.closest('.mb-3').find('.error-message'));
                        },
                        rules: {
                            name: {
                                required: true,
                                minlength: 8
                            },
                            email: {
                                required: true,
                                email: true
                            },
                            password: {
                                required: function(element) {
                                    return !$("#userForm input[name=id]").val();
                                },
                                minlength: 6
                            }
                        },
                        messages: {
                            name: {
                                required: "Por favor ingrese su nombre completo",
                                minlength: "El nombre completo debe tener al menos 8 caracteres"
                            },
                            email: {
                                required: "Por favor ingrese su correo electrónico",
                                email: "El valor no es una dirección de correo válida"
                            },
                            password: {
                                required: "La contraseña es obligatoria para nuevos usuarios",
                                minlength: "La contraseña debe tener al menos 6 caracteres"
                            }
                        },
                        submitHandler: function(form) {
                            var form = $("#userForm")[0],
                                data = new FormData(form);

                            $('#userForm :input').prop('disabled', true);

                            var url = $(form.id).val() ?
                                update_route.replace('0~0', $(form.id).val()) :
                                store_route;

                            $.ajax({
                                url: url,
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                timeout: 3000,
                                success: function(data) {
                                    $('#userForm :input').prop('disabled', false);

                                    if (data.errors) {
                                        $('#userForm .alert-errors').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                            '<div class="alert-body">' + data.errors + '</div>' +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                            '</div>');

                                    } else {
                                        $usersIndexAlert.html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                            '<div class="alert-body">' +
                                            '<p class="mb-0"><strong>' + data.success + '</strong></p>' +
                                            '</div>' +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                            '</div>');

                                        $('#userForm button[type=reset]').trigger('click');

                                        //@this.call('refreshUserCount');

                                        dt_user.ajax.reload();
                                    }
                                },
                                error: function(e) {
                                    $('#userForm :input').prop('disabled', false);

                                    $('#userForm .alert-errors').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        '<div class="alert-body">' + e.responseJSON.message + '</div>' +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                        '</div>');
                                }
                            });
                        }
                    });

            }


            // Previo de imagen de perfil
            updatePreviewImage = (event) => {
                var file = event.target.files[0],
                    reader = new FileReader();

                reader.onload = event => {
                    document.getElementById('user-image').setAttribute('src', event.target.result);
                };

                reader.readAsDataURL(file);
            }

            // Add User
            add_user = () => {
                let $offcanvasUser = $('#offcanvasUser');

                $('.offcanvas-title', $offcanvasUser).html('Crear usuario nuevo');
                $('.btn-submit', $offcanvasUser).html('Crear usuario');

                if ($('input[name=id]', $offcanvasUser).val()){
                    document.getElementById('userForm').reset();

                    $('input[name=id]', $offcanvasUser).val('');

                    $('#roles').trigger('change');

                    $('#user-image').prop("src", "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");

                    $('.alert-errors', $offcanvasUser).html('');
                }
            }


            Livewire.on('openModal', () => {
                setTimeout(() =>{
                    offcanvasUser.show();

                    load_js_form();

                    dt_user.ajax.reload();
                }, 1)
            });

            Livewire.on('afterDelete', () => {
                setTimeout(() =>{
                    var modalElement = document.getElementById('deleteUserModal'),
                        modalDelete = bootstrap.Modal.getInstance(modalElement);

                    modalDelete.hide();

                    load_js_form();

                    dt_user.ajax.reload();
                }, 1)
            });


            // (jquery)
            $(function () {
                let borderColor, bodyBg, headingColor;

                if (isDarkStyle) {
                    borderColor = config.colors_dark.borderColor;
                    bodyBg = config.colors_dark.bodyBg;
                    headingColor = config.colors_dark.headingColor;
                } else {
                    borderColor = config.colors.borderColor;
                    bodyBg = config.colors.bodyBg;
                    headingColor = config.colors.headingColor;
                }

                // Users datatable
                dt_user = $dt_user_table.DataTable({
                    ajax: '{{ url()->current() }}',
                    columns: [
                        // columns according to JSON
                        { data: 'id' },
                        { data: 'id' },
                        { data: 'name' },
                        { data: 'roles' },
                        { data: 'status' },
                        { data: 'created_at' },
                        { data: 'action' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            searchable: false,
                            orderable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            // User name and email
                            targets: 2,
                            responsivePriority: 3,
                            render: function (data, type, full, meta) {
                                var $name = full['name'],
                                    $email = full['email'],
                                    $image = full['avatar'];

                                if ($image) {
                                    // For Avatar image
                                    var $output =
                                        '<img src="' + $image + '" alt="Avatar" class="rounded-circle">';
                                } else {
                                    // For Avatar badge
                                    var $name = full['full_name'],
                                        $initials = $name.match(/\b\w/g) || [];
                                    $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                    $output = '<span class="avatar-initial rounded-circle>' + $initials + '</span>';
                                }

                                // Creates full output for row
                                var $row_output =
                                    '<div class="d-flex justify-content-start align-items-center user-name">' +
                                        '<div class="avatar-wrapper">' +
                                            '<div class="avatar avatar-sm me-4">' + $output + '</div>' +
                                        '</div>' +
                                        '<div class="d-flex flex-column">' +
                                            '<a href="' + show_route.replace('0~0', full['id']) + '" class="text-heading text-truncate"><span class="fw-medium">' + $name + '</span></a>' +
                                            '<small>' + $email + '</small>' +
                                        '</div>' +
                                    '</div>';

                                return $row_output;
                            }
                        },
                        {
                            // User Role
                            targets: 3,
                            render: function(data, type, full, meta) {
                                var $assignedTo = full['roles'],
                                    $output = '',
                                    roleBadgeObj = <?= json_encode($rows_roles) ?>;

                                for (var i = 0; i < $assignedTo.length; i++) {
                                    var val = $assignedTo[i];

                                    $output += roleBadgeObj[val];
                                }

                                return $output;
                            }
                        },
                        {
                            // User Status
                            targets: 4,
                            render: function (data, type, full, meta) {
                                var $status = full['status'];

                                return ('<span class="badge rounded-pill ' + statusObj[$status].class + '" text-capitalized>' + statusObj[$status].title + '</span>');

                            }
                        },
                        {
                            // Created
                            targets: 5,
                            render: function (data, type, full, meta) {
                                return  full['created_at'];
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Acciones',
                            searchable: false,
                            orderable: false,
                            render: function (data, type, full, meta) {
                                return ('<div class="d-flex align-items-center">' +
                                    '<a href="' + show_route.replace('0~0', full['id']) + '" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill"><i class="ti ti-eye ti-md"></i></a>' +
                                    '<a href="javascript:;"" wire:click.prevent="edit(' + full['id'] + ')" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill"><i class="ti ti-edit ti-md"></i></a>' +
                                    @can('system.users.destroy')
                                        '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                                        '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                            '<a href="javascript:;" class="dropdown-item delete-record">Eliminar</a>' +
                                        '</div>' +
                                    @endcan
                                '</div>');
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    dom:
                        '<"row"' +
                        '<"col-md-2"<"ms-n2"l>>' +
                        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"<"user_role dataTables_filter">fB>>' +
                        '>t' +
                        '<"row"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                    language: $.fn.dataTable.ext.datatable_spanish_default,
                    // Buttons with Dropdown
                    buttons: [
                        {
                        extend: 'collection',
                        className: 'btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light',
                        text: '<i class="ti ti-upload me-2 ti-xs"></i>Exportar',
                        buttons: [
                            {
                                extend: 'print',
                                text: '<i class="ti ti-printer me-2" ></i>Imprimir',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5],
                                    // prevent avatar to be print
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                    }
                                },
                                customize: function (win) {
                                    //customize print view for dark
                                    $(win.document.body)
                                    .css('color', headingColor)
                                    .css('border-color', borderColor)
                                    .css('background-color', bodyBg);
                                    $(win.document.body)
                                    .find('table')
                                    .addClass('compact')
                                    .css('color', 'inherit')
                                    .css('border-color', 'inherit')
                                    .css('background-color', 'inherit');
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="ti ti-file-text me-2" ></i>Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5],
                                    // prevent avatar to be display
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                    }
                                }
                            },
                            {
                                extend: 'excel',
                                text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5],
                                    // prevent avatar to be display
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                    }
                                }
                            },
                            /*
                            {
                                extend: 'pdf',
                                text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5],
                                    // prevent avatar to be display
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                    }
                                }
                            },
                            */
                            {
                                extend: 'copy',
                                text: '<i class="ti ti-copy me-2" ></i>Copiar',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [1, 2, 3, 4, 5],
                                    // prevent avatar to be display
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                    }
                                }
                            }
                        ]
                        },
                        @can('system.users.create') {
                            text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Nuevo usuario</span>',
                            className: 'add-new btn btn-primary waves-effect waves-light',
                            attr: {
                                'onclick': 'add_user()',
                                'data-bs-toggle': 'offcanvas',
                                'data-bs-target': '#offcanvasUser'
                            }
                        }
                        @endcan
                    ],
                    // For responsive popup
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
                                    ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                        '<td>' + col.title + ':' + '</td> ' +
                                        '<td>' + col.data + '</td>' +
                                        '</tr>'
                                    : '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    },
                    initComplete: function () {
                        this.api()
                            .columns(3)
                            .every(function () {
                                var column = this,
                                    select = $('{!! $roles_html_select !!}')
                                        .appendTo('.user_role')
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                            column.search(val? val: '', true, false).draw();
                                        });
                            });
                    }
                });


                // Delete Record
                $('.datatables-users tbody')
                    .on('click', '.delete-record', function () {
                        var tr = $(this).closest('tr'),
                            data = dt_user.row(tr).data();

                        $('#deleteUserModal').modal('show');
                        $('#deleteUserModal .name').html(data.id + ': ' + data.name);

                        $('#deleteUserModal').data('userId', data.id);
                    });


                // Attach the event listener to the submit button
                $('#deleteUserModal .btn-submit')
                    .on('click', function (e) {
                        e.preventDefault();

                        @this.call('delete', $('#deleteUserModal').data('userId'));
                    });


                // Filter form control to default size
                // ? setTimeout used for multilingual table initialization
                setTimeout(() => {
                    $('.dataTables_filter .form-control').removeClass('form-control-sm');
                    $('.dataTables_length .form-select').removeClass('form-select-sm');
                }, 300);


                load_js_form();
            });

        });
    </script>
@endpush
