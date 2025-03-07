<!-- Permission Table -->
<div class="card">
    <div class="card-datatable table-responsive">
        <table class="datatables-permissions table border-top">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Nombre</th>
                    <th>Asignado a</th>
                    <th>Creado</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!--/ Permission Table -->


<?php
/*
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                // Datatable
                var dt_permission = $('.datatables-permissions')
                    .DataTable({
                        ajax: '{{ url()->current() }}',
                        columns: [
                            // columns according to JSON
                            {data: ''},
                            {data: 'id'},
                            {data: 'name'},
                            {data: 'assigned_to'},
                            {data: 'created_at'},
                            //{data: ''}
                        ],
                        columnDefs: [
                            {
                                // For Responsive
                                className: 'control',
                                orderable: false,
                                searchable: false,
                                responsivePriority: 2,
                                targets: 0,
                                render: function(data, type, full, meta) {
                                    return '';
                                }
                            },
                            {
                                targets: 1,
                                searchable: false,
                                visible: false
                            },
                            {
                                // Name
                                targets: 2,
                                render: function(data, type, full, meta) {
                                    return "<span data-id=" + full.id + ">" + data + "</span><br>" +
                                        '<small>' + (typeof(full['sub_group']) == 'string'? full['sub_group']: '') + "</small>";
                                }
                            },
                            {
                                // assigned_to
                                targets: 3,
                                orderable: false,
                                render: function(data, type, full, meta) {
                                    var $assignedTo = full['assigned_to'],
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
                                // Created at
                                targets: 4,
                                orderable: false
                            },
                        ],
                        order: [
                            [1, 'asc']
                        ],
                        dom:
                        '<"row mx-1"' +
                        '<"col-sm-12 col-md-3" l>' +
                        '<"col-sm-12 col-lg-9"<"dt-action-buttons d-flex align-items-center justify-content-lg-end justify-content-center flex-md-nowrap flex-wrap"<"me-1"f><"user_role mt-50 width-200 me-1">B>>' +
                        '>t' +
                        '<"row mx-2"' +
                        '<"col-sm-12 col-md-6"i>' +
                        '<"col-sm-12 col-md-6"p>' +
                        '>',
                        language: $.fn.dataTable.ext.datatable_spanish_default,
                        // Buttons with Dropdown
                        buttons: [],
                        // For responsive popup
                        responsive: {
                            details: {
                                display: $.fn.dataTable.Responsive.display.modal({
                                    header: function(row) {
                                        var data = row.data();

                                        return 'Detalles del permiso';
                                    }
                                }),
                                type: 'column',
                                renderer: function(api, rowIdx, columns) {
                                    var data = $.map(columns, function(col, i) {
                                        return col.title !== ''? // ? Do not show row in modal popup if title is blank (for check box)
                                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                                '<td>' + col.name + ':' + '</td> ' +
                                                '<td>' + col.data + '</td>' +
                                            '</tr>' :
                                            '';
                                    }).join('');

                                    return data ? $('<table class="table table-striped"/><tbody />').append(data) : false;
                                }
                            }
                        },

                        initComplete: function() {
                            // Adding role filter once table initialized
                            this.api()
                                .columns(3)
                                .every(function() {
                                    var column = this;

                                    var select = $('{!! $roles_html_select !!}')
                                        .appendTo('.user_role')
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                            column.search(val? val: '', true, false).draw();
                                        });
                                });
                        }
                    });

            });
        });
    </script>
*/

?>
