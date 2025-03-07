<!-- Delete Role Card Modal -->
<div class="modal fade" id="roleDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-delete-role">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="role-title mb-2">Eliminar Rol</h3>
                    <p class="role-subtitle text-muted">Se eliminará de forma definitiva</p>
                </div>
                <form id="deleteRoleForm" class="row g-3">
                    <input type="hidden" name="id" wire:model='deleteRoleId'>
                    <div class="col-12">
                        <p class="confirmation-text"></p>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-danger me-sm-3 me-1">Eliminar rol</button>
                        <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Delete Role Card Modal -->
