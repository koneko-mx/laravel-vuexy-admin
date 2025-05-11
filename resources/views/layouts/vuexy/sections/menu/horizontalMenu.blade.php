@php
    $configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <div class="{{ $containerNav }} d-flex h-100">
        <ul class="menu-inner pb-2 pb-xl-0">
            @foreach ($vuexyMenu as $label => $menu)
                @include('vuexy-admin::layouts.vuexy.sections.menu._item', ['label' => $label, 'item' => $menu])
            @endforeach
        </ul>
    </div>
</aside>
