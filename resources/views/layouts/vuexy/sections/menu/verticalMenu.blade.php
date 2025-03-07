@php
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    @if(!isset($navbarFull))
        <div class="app-brand demo">
            <a href="{{ route('admin.core.home.index') }}" class="app-brand-link">
                <span class="app-brand-logo demo">
                  <img src="{{ asset('storage/' . $_admin['image_logo']['small']) }}" alt="{{ $_admin['app_name'] }}" />
                </span>
                <span class="app-brand-text demo menu-text fw-bold">{{ $_admin['app_name'] }}</span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
            </a>
        </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($vuexyMenu as $menuName => $menu)
            @if (isset($menu->menuHeader))
                <li class="menu-header small">
                    <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                </li>
            @else
                {{-- main menu --}}
                <li class="menu-item {{ isset($menu['active']) && $menu['active'] ? 'active open' : '' }}">
                    <a href="{{ $menu['url'] ?? 'javascript:void(0);' }}" class="menu-link {{ isset($menu['submenu']) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu['target']) and !empty($menu['target'])) target="{{ $menu['target'] }}" @endif>
                        @isset($menu['icon'])
                            <i class="{{ $menu['icon'] }}"></i>
                        @endisset
                        <div>{{ $menuName }}</div>
                        @isset($menu['badge'])
                            <div class="badge bg-{{ $menu['badge'][0] }} rounded-pill ms-auto">{{ $menu['badge'][1] }}</div>
                        @endisset
                    </a>
                    @isset($menu['submenu'])
                        @include('vuexy-admin::layouts.vuexy.sections.menu.submenu', ['menu' => $menu['submenu']])
                    @endisset
                </li>
            @endif
        @endforeach
    </ul>

</aside>
