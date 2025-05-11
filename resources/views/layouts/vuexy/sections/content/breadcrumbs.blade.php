<!-- Breadcrumbs: Start -->
@if($vuexyBreadcrumbs)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($vuexyBreadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}">
                    @if(!$breadcrumb['active'] && isset($breadcrumb['link']))
                        <a href="{{ $breadcrumb['link'] }}">{{ $breadcrumb['name'] }}</a>
                    @else
                        {{ $breadcrumb['name'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
<!-- Breadcrumbs: End -->
