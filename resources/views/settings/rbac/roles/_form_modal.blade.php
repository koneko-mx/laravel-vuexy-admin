<!-- Add Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-3 p-md-5">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h3 class="role-title mb-2">{{ $title }}</h3>
                    <p class="role-subtitle text-muted">Configuración de rol de usuario</p>
                </div>
                <!-- Role form -->
                <form id="roleForm" class="row g-3" method="POST">
                    <input type="hidden" name="id" wire:model='roleId'>
                    <div class="col-xl-4 col-md-6 col-12 col-inputs">
                        <div class="mb-1">
                            <label for="name" class="form-label">Nombre del rol</label>
                            <input type="text" name="name" value="" class="form-control" placeholder="Nombre del rol" tabindex="-1" wire:model="name">
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12 col-inputs">
                        <div class="mb-1">
                            <label for="style" class="form-label">CSS style</label>
                            <select id="style" name="style" wire:model="style" class="form-select">
                                <option value="">Estilos CSS</option>
                                <option value="info">.info</option>
                                <option value="success">.success</option>
                                <option value="primary">.primary</option>
                                <option value="secondary">.secondary</option>
                                <option value="dark">.dark</option>
                                <option value="danger">.danger</option>
                                <option value="warning">.warning</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Permission table -->
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                    <tr>
                                        <td class="text-nowrap fw-bolder">
                                            <h4 class="mt-2 pt-50">Permisos de usuario</h4>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll" />
                                                <label class="form-check-label" for="selectAll">Seleccionar todo</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach ($permissions as $key => $permission)
                                        <tr class="permission-row">
                                            <td class="text-nowrap fw-bolder">{{ $permission['group_name'] }} - {{ $permission['sub_group_name'] }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @isset($permission['allow'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Permitir
                                                                <input class="form-check-input" type="checkbox" data-type="allow" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['allow']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                    @isset($permission['view'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Leer
                                                                <input class="form-check-input" type="checkbox" data-type="view" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['view']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                    @isset($permission['create'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Crear
                                                                <input class="form-check-input" type="checkbox" data-type="create" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['create']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                    @isset($permission['edit'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Modificar
                                                                <input class="form-check-input" type="checkbox" data-type="edit" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['edit']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                    @isset($permission['cancel'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Cancelar
                                                                <input class="form-check-input" type="checkbox" data-type="cancel" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['cancel']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                    @isset($permission['delete'])
                                                        <div class="form-check me-3 me-lg-5">
                                                            <label class="form-check-label"> Eliminar
                                                                <input class="form-check-input" type="checkbox" data-type="delete" wire:model="permissionsInputs.{{ str_replace('.', '_', $permission['delete']) }}">
                                                            </label>
                                                        </div>
                                                    @endisset
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ $btn_submit_text }}</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </form>
                <!--/ Add role form -->
            </div>
        </div>
    </div>
</div>
<!--/ Add Role Modal -->
