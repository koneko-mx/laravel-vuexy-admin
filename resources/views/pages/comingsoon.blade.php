@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Muy pronto!')

@push('page-style')
    @vite(['/resources/scss/pages/page-misc.scss'])
@endsection

@section('content')
    <!-- Coming Soon -->
    <div class="container-xxl container-p-y py-4">
        <div class="misc-wrapper">
            <h4 class="mb-2 mx-2">Lanzaremos pronto 🚀</h4>
            <p class="mb-6 mx-2">Nuestro sitio web se inaugurará pronto. ¡Regístrese para recibir una notificación cuando esté listo!</p>
            <form onsubmit="return false">
                <div class="mb-0">
                    <div class="mb-0 d-flex gap-4">
                        <input type="text" class="form-control" placeholder="Ingresa tu correo electrónico" autofocus>
                        <button type="submit" class="btn btn-primary">Notificar</button>
                    </div>
                </div>
            </form>
            <div class="mt-12">
                <img src="{{ asset('vendor/vuexy-admin/img/illustrations/page-misc-launching-soon.png') }}" alt="page-misc-launching-soon" width="263" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('vendor/vuexy-admin/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" height="355" alt="page-misc-coming-soon" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
    <!-- /Coming soon -->
@endsection
