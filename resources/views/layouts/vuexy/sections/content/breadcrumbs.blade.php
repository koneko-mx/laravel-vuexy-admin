<!-- Breadcrumbs: Start -->
@if($vuexyBreadcrumbs)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($vuexyBreadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{ isset($breadcrumb['active']) && $breadcrumb['active']? 'active': '' }}">
                    @php
                        if(isset($breadcrumb['route']) && isset($breadcrumb['link']) == false)
                            $breadcrumb['link'] = route($breadcrumb['route']);
                    @endphp
                    @isset($breadcrumb['link'])
                        <a href="{{ $breadcrumb['link'] }}">{{ $breadcrumb['name'] }}</a>
                    @else
                        {{ $breadcrumb['name'] }}
                    @endisset
                </li>
            @endforeach
        </ol>
    </nav>
@endif
<!-- Breadcrumbs: End -->
