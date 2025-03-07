@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Usuario | ' . $user->name)

@section('vendor-style')
    {{-- Page Css files --}}
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/animate-css/animate.scss',

        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/select2/select2.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
@endsection

@push('page-style')
    {{-- Page Css files --}}
    @vite([
        'Resources/vendor/scss/pages/page-user-view.scss'
    ])
@endsection

@section('content')
    <section class="app-user-view-account">
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mt-3 mb-2"
                                    src="{{ $user->profile_photo_url }}"
                                    height="110"
                                    width="110"
                                    alt="User avatar"/>
                                <div class="user-info text-center">
                                    <h4>{{ $user->name }}</h4>
                                    <span class="badge bg-light-secondary">Author</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around my-2 pt-75">
                            <div class="d-flex align-items-start me-2">
                                <span class="badge bg-light-primary p-75 rounded">
                                    <i data-feather="check" class="font-medium-2"></i>
                                </span>
                                <div class="ms-75">
                                    <h4 class="mb-0">1.23k</h4>
                                    <small>Tasks Done</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <span class="badge bg-light-primary p-75 rounded">
                                    <i data-feather="briefcase" class="font-medium-2"></i>
                                </span>
                                <div class="ms-75">
                                    <h4 class="mb-0">568</h4>
                                    <small>Projects Done</small>
                                </div>
                            </div>
                        </div>
                        <h4 class="fw-bolder border-bottom pb-50 mb-1">Detalles</h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">Nombre:</span>
                                    <span>{{ $user->name }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">Correo eletrónico:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">Status:</span>
                                    @if ($user->status)
                                        <span class="badge bg-light-{{ Koneko\VuexyAdmin\Models\User::$statusListClass[$user->status] }}">{{ Koneko\VuexyAdmin\Models\User::$statusList[$user->status] }}</span>
                                    @endif
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">Role:</span>
                                    @foreach ($user->getRoleNames() as $role)
                                        <span class="badge bg-light-{{ Spatie\Permission\Models\Role::where('name', $role)->first()->style }}">{{ $role }}</span>
                                    @endforeach
                                    </li>
                            </ul>
                            <div class="d-flex justify-content-center pt-2">
                                <a href="javascript:;" class="btn btn-primary me-1" data-bs-target="#editUser" data-bs-toggle="modal">Editar</a>
                                <a href="javascript:;" class="btn btn-outline-danger suspend-user">Suspender</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- User Pills -->
                <ul class="nav nav-pills mb-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{asset('app/user/view/account')}}">
                            <i data-feather="user" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">Account</span></a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{asset('app/user/view/security')}}">
                            <i data-feather="lock" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">Security</span>
                        </a>
                    </li>
                </ul>
                <!--/ User Pills -->

                <!-- Activity Timeline -->
                <div class="card">
                    <h4 class="card-header">User Activity Timeline</h4>
                    <div class="card-body pt-1">
                        <ul class="timeline ms-50">
                            <li class="timeline-item">
                                <span class="timeline-point timeline-point-indicator"></span>
                                <div class="timeline-event">
                                    <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                        <h6>User login</h6>
                                        <span class="timeline-event-time me-1">12 min ago</span>
                                    </div>
                                    <p>User login at 2:12pm</p>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="timeline-point timeline-point-warning timeline-point-indicator"></span>
                                <div class="timeline-event">
                                    <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                        <h6>Meeting with john</h6>
                                        <span class="timeline-event-time me-1">45 min ago</span>
                                    </div>
                                    <p>React Project meeting with john @10:15am</p>
                                    <div class="d-flex flex-row align-items-center mb-50">
                                        <div class="avatar me-50">
                                            <img
                                                src="{{asset('images/portrait/small/avatar-s-7.jpg')}}"
                                                alt="Avatar"
                                                width="38"
                                                height="38"
                                            />
                                        </div>
                                        <div class="user-info">
                                            <h6 class="mb-0">Leona Watkins (Client)</h6>
                                            <p class="mb-0">CEO of pixinvent</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="timeline-point timeline-point-info timeline-point-indicator"></span>
                                <div class="timeline-event">
                                    <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                        <h6>Create a new react project for client</h6>
                                        <span class="timeline-event-time me-1">2 day ago</span>
                                    </div>
                                    <p>Add files to new design folder</p>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="timeline-point timeline-point-danger timeline-point-indicator"></span>
                                <div class="timeline-event">
                                    <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                        <h6>Create Invoices for client</h6>
                                        <span class="timeline-event-time me-1">12 min ago</span>
                                    </div>
                                    <p class="mb-0">Create new Invoices and send to Leona Watkins</p>
                                    <div class="d-flex flex-row align-items-center mt-50">
                                        <img class="me-1" src="{{asset('images/icons/pdf.png')}}" alt="data.json" height="25" />
                                        <h6 class="mb-0">Invoices.pdf</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /Activity Timeline -->
            </div>
            <!--/ User Content -->
        </div>
    </section>
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/moment/moment.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',

        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/cleavejs/cleave.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/cleavejs/cleave-phone.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/select2/select2.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js'
    ])
@endsection

@push('page-script')
    @vite([
        'Resources/js/modal-edit-user.js',
        'Resources/js/app-user-view.js',
        'Resources/js/app-user-view-account.js',
        'Resources/js/pages-profile.js'
    ])
@endpush
