@php
    $meta        = $item['_meta'] ?? [];
    $slug        = $item['_slug'] ?? null;
    $icon        = $meta['icon'] ?? $item['icon'] ?? null;
    $count       = $meta['count'] ?? 0;
    $url         = $item['url'] ?? 'javascript:void(0);';
    $label       = $meta['label'] ?? $label;
    $badge       = $meta['badge'] ?? null;
    $target      = isset($item['target']) ? 'target="'.$item['target'].'"' : '';
    $active      = $meta['active'] ?? false;
    $hasSubmenu  = isset($item['submenu']) && is_array($item['submenu']);
    $autoId      = $meta['auto_id'] ?? null;

    $classes     = 'menu-item' . ($active ? ' active' : '');
    $linkClass   = 'menu-link' . ($hasSubmenu ? ' menu-toggle' : '');
@endphp

<li class="{{ $classes }}">
    <a href="{{ $url }}" class="{{ $linkClass }}" {!! $target !!}>
        @isset($icon)
            <i class="menu-icon tf-icons {{ $icon }}"></i>
        @endisset
        <div>{{ $label }}</div>
        @isset($badge)
            <div class="badge bg-{{ $badge['level'] ?? 'secondary' }} rounded-pill ms-auto">{{ $badge['label'] ?? '' }}</div>
        @endisset
    </a>

    @if ($hasSubmenu)
        <ul class="menu-sub">
            @foreach ($item['submenu'] as $subLabel => $subItem)
                @include('vuexy-admin::layouts.vuexy.sections.menu._item', ['label' => $subLabel, 'item' => $subItem])
            @endforeach
        </ul>
    @endif
</li>
