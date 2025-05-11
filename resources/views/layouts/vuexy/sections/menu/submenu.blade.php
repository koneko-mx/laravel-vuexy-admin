<ul class="menu-sub">
    @foreach ($menu as $submenuName => $submenu)
        <li class="menu-item {{ isset($submenu['active']) && $submenu['active'] ? 'active open' : '' }}">
            <a href="{{ $submenu['url'] ?? 'javascript:void(0);' }}" class="menu-link {{ isset($submenu['submenu']) ? 'menu-toggle' : '' }}" @if (isset($submenu['target']) and !empty($submenu['target'])) target="{{ $submenu['target'] }}" @endif>
                @isset($submenu['icon'])
                    <i class="menu-icon tf-icons {{ $submenu['icon'] }}"></i>
                @endisset
                <div>{{ $submenuName }}</div>
                @isset($submenu['badge'])
                    <div class="badge bg-{{ $submenu['badge'][0] }} rounded-pill ms-auto">{{ $submenu['badge'][1] }}</div>
                @endisset
            </a>
            @isset($submenu['submenu'])
                @include('vuexy-admin::layouts.vuexy.sections.menu.submenu', ['menu' => $submenu['submenu']])
            @endisset
        </li>
    @endforeach
</ul>
