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
                    <a href="{{ route('admin.core.pages.home.index') }}" class="app-brand-link">
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
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item navbar-search-wrapper mb-0">
                            <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                                <i class="ti ti-search ti-md me-2 me-lg-4 ti-lg"></i>
                                <span class="d-none d-md-inline-block text-muted fw-normal">Buscar (Ctrl+/)</span>
                            </a>
                        </div>
                    </div>
                    <!-- /Search -->
                @endif
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    @if(isset($menuHorizontal))
                        <!-- Search -->
                        <li class="nav-item navbar-search-wrapper">
                            <a class="nav-link btn btn-text-secondary btn-icon rounded-pill search-toggler" href="javascript:void(0);">
                            <i class="ti ti-search ti-md"></i>
                            </a>
                        </li>
                        <!-- /Search -->
                    @endif

                    <!-- Language -->
                    <li class="nav-item dropdown-language dropdown">
                        <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <i class='ti ti-language rounded-circle ti-md'></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item pr-2 {{ app()->getLocale() === 'es' ? 'active' : '' }}" href="{{url('lang/es')}}" data-language="es" data-text-direction="ltr">
                                    <img src="{{ asset('vendor/vuexy-admin/flag-icons/flags/1x1/mx.svg') }}" style="height:16px" class="inline" alt="">
                                    <span class="ml-1">Español México</span>
                                </a>
                            </li>
                        <li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() === 'co' ? 'active' : '' }}" href="{{url('lang/co')}}" data-language="co" data-text-direction="ltr">
                                <img src="{{ asset('vendor/vuexy-admin/flag-icons/flags/1x1/co.svg') }}" style="height:16px" class="inline" alt="">
                                <span class="ml-1">Español Colombia</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{url('lang/en')}}" data-language="en" data-text-direction="ltr">
                                <img src="{{ asset('vendor/vuexy-admin/flag-icons/flags/1x1/us.svg') }}" style="height:16px" class="inline" alt="">
                                <span class="ml-1">English</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{url('lang/fr')}}" data-language="fr" data-text-direction="ltr">
                                <img src="{{ asset('vendor/vuexy-admin/flag-icons/flags/1x1/fr.svg') }}" style="height:16px" class="inline" alt="">
                                <span class="ml-1">French</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{url('lang/de')}}" data-language="de" data-text-direction="ltr">
                                <img src="{{ asset('vendor/vuexy-admin/flag-icons/flags/1x1/de.svg') }}" style="height:16px" class="inline" alt="">
                                <span class="ml-1">German</span>
                            </a>
                        </li>
                        </ul>
                    </li>
                    <!--/ Language -->


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
                    @livewire('vuexy-admin::vuexy-quicklinks')
                    <!-- Quick links -->

                    <!-- Notification -->
                    @if($vuexyNotifications)
                        @foreach ($vuexyNotifications as $vuexyNotification)

                        @endforeach
                    @endif
                    <!--/ Notification -->

                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center ml-2" href="javascript:void(0);" data-bs-toggle="dropdown">
                            @if (Auth::check())
                                <div class="user-nav me-2 d-none d-sm-block">
                                    <span class="user-name d-block text-end">{{ Auth::user()->full_name }}</span>
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
                                    <a class="dropdown-item" href="{{ route('admin.core.user.profile.index') }}">
                                        <i class="ti ti-user-cog me-2 ti-sm"></i>
                                        <span class="align-middle">Cuenta de usuario</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.core.pages.about.index') }}">
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

            <!-- Search Small Screens -->
            <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
                <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
                <i class="ti ti-x search-toggler cursor-pointer"></i>
            </div>
            <!--/ Search Small Screens -->
            @if(isset($navbarDetached) && $navbarDetached == '')
        </div>
        @endif
    </nav>
    <!-- / Navbar -->
