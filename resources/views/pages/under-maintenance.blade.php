@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', '¡En mantenimiento!')

@push('page-style')
    @vite(['/resources/scss/pages/page-misc.scss'])
@endsection

@section('content')
    <!--Under Maintenance -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h4 class="mb-2 mx-2">¡En mantenimiento! 🚧</h4>
            <p class="mb-6 mx-2">Disculpe las molestias, pero estamos realizando tareas de mantenimiento en estos momentos.</p>
            <a href="{{ route('admin.core.pages.home.index') }}" class="btn btn-primary">Regresar al inicio</a>
            <div class="mt-12">
                <img src="{{ asset('vendor/vuexy-admin/img/illustrations/page-misc-under-maintenance.png') }}" alt="page-misc-under-maintenance" width="550" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="container-fluid misc-bg-wrapper misc-under-maintenance-bg-wrapper">
        <img src="{{ asset('vendor/vuexy-admin/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" height="355" alt="page-misc-under-maintenance" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
    <!-- /Under Maintenance -->
@endsection
