@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
$containerNav = ($configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme" id="layout-navbar">
    @endif
    @if(isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{$containerNav}}">
            @endif

            <!--  Brand demo (display only for navbar-full and hide on below xl) -->
            @if(isset($navbarFull))
                <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                    <a href="{{ route('admin.core.home.index') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('storage/' . $_admin['image_logo']['small']) }}" alt="{{ $_admin['app_name'] }}" />
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">{{ $_admin['app_name'] }}</span>
                    </a>
                    @if(isset($menuHorizontal))
                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                            <i class="ti ti-x ti-md align-middle"></i>
                        </a>
                    @endif
                </div>
            @endif

            <!-- ! Not required for layout-without-menu -->
            @if(!isset($navbarHideToggle))
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="ti ti-menu-2 ti-md"></i>
                    </a>
                </div>
            @endif

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                @if(!isset($menuHorizontal))
                    <!-- Search -->
                    @if ($vuexySearch)
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper mb-0">
                                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                                    <i class="ti ti-search ti-md me-2 me-lg-4 ti-lg"></i>
                                    <span class="d-none d-md-inline-block text-muted fw-normal">Buscar (Ctrl+/)</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    <!-- /Search -->
                @endif
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    @if(isset($menuHorizontal))
                        <!-- Search -->
                        @if ($vuexySearch)
                            <li class="nav-item navbar-search-wrapper">
                                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill search-toggler" href="javascript:void(0);">
                                    <i class="ti ti-search ti-md"></i>
                                </a>
                            </li>
                        @endif
                        <!-- /Search -->
                    @endif

                    @if($configData['hasCustomizer'] == true)
                        <!-- Style Switcher -->
                        <li class="nav-item dropdown-style-switcher dropdown">
                            <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <i class='ti ti-md'></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                        <span class="align-middle"><i class='ti ti-sun ti-md me-3'></i>Claro</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                        <span class="align-middle"><i class="ti ti-moon-stars ti-md me-3"></i>Obscuro</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                        <span class="align-middle"><i class="ti ti-device-desktop-analytics ti-md me-3"></i>Sistema</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- / Style Switcher -->
                    @endif

                    <!-- Quick links  -->
                    @if ($vuexyQuickLinks)
                        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
                            <a class="nav-link btn btn-text-secondary btn-icon rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <i class='ti ti-layout-grid-add ti-md'></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0">
                                <div class="dropdown-menu-header border-bottom">
                                    <div class="dropdown-header d-flex align-items-center py-3">
                                        <h6 class="mb-0 me-auto">Atajos</h6>
                                        @if($vuexyQuickLinks['current_page_in_list'])
                                            <a href="javascript:void(0)" class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-remove" data-bs-toggle="tooltip" data-bs-placement="top" title="Remover atajo"><i class="ti ti-trash text-heading"></i></a>
                                        @else
                                            @if($vuexyQuickLinks['totalLinks'] < config('vuexy.custom.maxQuickLinks'))
                                                <a href="javascript:void(0)" class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-add" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar atajo"><i class="ti ti-plus text-heading"></i></a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="dropdown-shortcuts-list scrollable-container">
                                    @foreach  ($vuexyQuickLinks['rows'] as $quickLinksRow)
                                        <div class="row row-bordered overflow-visible g-0">
                                            @foreach  ($quickLinksRow as $key => $quickLink)
                                                <div class="dropdown-shortcuts-item col @if($quickLink['route'] === Route::currentRouteName()) active @endif">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                                        <i class="ti ti-{{ $quickLink['icon'] }} ti-26px text-heading"></i>
                                                    </span>
                                                    <a href="{{ $quickLink['url'] }}" class="stretched-link">{{ $quickLink['title'] }}</a>
                                                    <small>{{ $quickLink['subtitle'] }}</small>
                                                </div>
                                                @if ($key == 0 && !isset($quickLinksRow[1]))
                                                    <div class="dropdown-shortcuts-item col"></div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endif
                    <!-- Quick links -->

                    <!-- Notification -->
                    {!! $vuexyNotifications !!}
                    <!--/ Notification -->

                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown">
                            @if (Auth::check())
                                <div class="user-nav me-2 d-none d-sm-block">
                                    <span class="user-name d-block text-end">{{ Auth::user()->fullname }}</span>
                                    <span class="user-email d-block text-end">{{ Auth::user()->email }}</span>
                                </div>
                            @endif
                            <div class="avatar {{ Auth::check()? 'avatar-online': '' }} ">
                                <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('vendor/vuexy-admin/img/avatar/generic.svg') }}" alt class="h-auto rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (Auth::check())
                                <li class="d-block d-sm-none">
                                    <a class="dropdown-item" href="javascript:;">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ Auth::user()->profile_photo_url }}" alt class="h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-medium d-block">
                                                    {{ Auth::user()->name }}
                                                </span>
                                                <small class="text-muted">{{ Auth::user()->email }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="d-block d-sm-none">
                                    <div class="dropdown-divider"></div>
                                </li>
                            @endif
                            @if (Auth::check())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.core.user-profile.index') }}">
                                        <i class="ti ti-user-cog me-2 ti-sm"></i>
                                        <span class="align-middle">Configuración de cuenta</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.core.about.index') }}">
                                    <i class='ti ti-cat me-2'></i>
                                    <span class="align-middle">Acerca de</span>
                                </a>
                            </li>
                            @if (Auth::check())
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="btn btn-sm btn-danger d-flex" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class='ti ti-logout me-2'></i>
                                        <span class="align-middle">Cerrar sesión</span>
                                    </a>
                                </li>
                                <form method="POST" id="logout-form" action="{{ route('logout') }}">
                                    @csrf
                                </form>
                            @else
                                <li>
                                    <a class="dropdown-item" href="{{ route('login') }}">
                                        <i class='ti ti-login me-2'></i>
                                        <span class="align-middle">Iniciar sesión</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <!--/ User -->
                </ul>
            </div>

            @if ($vuexySearch)
                <!-- Search Small Screens -->
                <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
                    <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Buscar..." aria-label="Buscar...">
                    <i class="ti ti-x search-toggler cursor-pointer"></i>
                </div>
                <!--/ Search Small Screens -->
            @endif
            @if(isset($navbarDetached) && $navbarDetached == '')
        </div>
        @endif
    </nav>
    <!-- / Navbar -->
