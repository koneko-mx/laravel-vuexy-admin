@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appClasses();

/* Display elements */
$customizerHidden = ($customizerHidden ?? '');
@endphp

@extends('vuexy-admin::layouts.vuexy.commonMaster' )

@section('layoutContent')
    <!-- Content -->
    @yield('content')
    <!--/ Content -->
@endsection
