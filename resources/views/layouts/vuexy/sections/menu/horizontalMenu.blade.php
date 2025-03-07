@php
    $configData = Helper::appClasses();
@endphp

<!-- Horizontal Menu -->
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <div class="{{$containerNav}} d-flex h-100">
        <ul class="menu-inner pb-2 pb-xl-0">
            @foreach ($vuexyMenu as $menuName => $menu)
                <li class="menu-item {{ isset($menu['active']) && $menu['active'] ? 'active' : '' }}">
                    <a href="{{ $menu['url'] ?? 'javascript:void(0);' }}" class="menu-link {{ isset($menu['submenu']) ? 'menu-toggle' : '' }}" @if (isset($menu['target']) and !empty($menu['target'])) target="{{ $menu['target'] }}" @endif>
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
            @endforeach
        </ul>
    </div>
</aside>
<!--/ Horizontal Menu -->
