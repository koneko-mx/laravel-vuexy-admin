<div class="row g-4 mb-4 text-right">
    <div class="col-sm-6 col-xl-3"></div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <div class="d-flex align-items-center my-2">
                            <h3 class="mb-0 mx-4">{{ $enabled }}</h3>
                        </div>
                        <p class="mb-0">Usuarios activos</p>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-user-plus ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <div class="d-flex align-items-center my-2">
                            <h3 class="mb-0 mx-4">{{ $disabled }}</h3>
                        </div>
                        <p class="mb-0">Usuarios suspendidos</p>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-user-check ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <div class="d-flex align-items-center my-2">
                            <h3 class="mb-0 mx-4">{{ $total }}</h3>
                        </div>
                        <p class="mb-0">Total de usuarios</p>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-user ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
