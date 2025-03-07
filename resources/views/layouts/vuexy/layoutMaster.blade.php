@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helper::appClasses();
@endphp

@isset($configData["layout"])
    @include((( $configData["layout"] === 'horizontal')
        ? 'vuexy-admin::layouts.vuexy.horizontalLayout'
        : (( $configData["layout"] === 'blank')
            ? 'vuexy-admin::layouts.vuexy.blankLayout'
            : (($configData["layout"] === 'front')
                ? 'vuexy-admin::layouts.vuexy.layoutFront'
                : 'vuexy-admin::layouts.vuexy.contentNavbarLayout') )))
@endisset
