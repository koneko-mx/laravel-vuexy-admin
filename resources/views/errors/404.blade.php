@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', '404 Not Found')

@push('page-style')
    <!-- Page -->
    @vite(['/resources/scss/pages/page-misc.scss'])
@endsection

@section('content')
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2 fs-xxlarge" style="line-height: 6rem;font-size: 6rem;">404</h1>
            <h4 class="mb-2 mx-2">Página no encontrada ⚠️</h4>
            <p class="mb-6 mx-2">
                {{ __('errors.page_not_found') }}
            </p>
            <a href="{{ route('admin.core.pages.home.index') }}" class="btn btn-primary mb-10">Regresar al inicio</a>
            <div class="mt-4">
                <img src="{{ asset('vendor/vuexy-admin/img/illustrations/page-misc-error.png') }}" alt="page-misc-error" width="225" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('vendor/vuexy-admin/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" height="355" alt="page-misc-error" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
    <!-- /Error -->
@endsection
