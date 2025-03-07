@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Registro de usuarios')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
@endsection

@push('page-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/scss/pages/page-auth.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js'
    ])
@endsection

@push('page-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/js/auth/pages-auth.js'
    ])
@endpush

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
                <img src="{{ asset('storage/' . $_admin['image_logo']['small']) }}" alt="{{ $_admin['app_name'] }}" />
            </span>
            <span class="app-brand-text demo text-heading fw-bold">{{ $_admin['app_name'] }}</span>
        </a>
        <!-- /Logo -->

        <div class="authentication-inner row m-0">
            <!-- Texto Izquierdo -->
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('vendor/vuexy-admin/img/illustrations/auth-register-illustration-'.$configData['style'].'.png') }}" alt="registro-cubierta" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-register-illustration-light.png" data-app-dark-img="illustrations/auth-register-illustration-dark.png">
                    <img src="{{ asset('vendor/vuexy-admin/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="registro-cubierta" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
                </div>
            </div>
            <!-- /Texto Izquierdo -->

            <!-- Registro -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Empieza tu aventura 🚀</h4>
                    <p class="mb-6">Gestiona tu aplicación de manera sencilla y divertida.</p>

                    <!-- Formulario de Registro -->
                    <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-6 fv-row">
                            <label for="name" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa tu nombre de usuario" autofocus required>
                        </div>
                        <div class="mb-6 fv-row">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-6 fv-row form-password-toggle">
                            <label class="form-label" for="password">Contraseña</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-6 fv-row form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        <div class="mb-6 fv-row mt-8">
                            <div class="form-check mb-8 ms-2">
                                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required>
                                <label class="form-check-label" for="terms-conditions">
                                    Acepto la <a href="javascript:void(0);">política de privacidad y términos</a>
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100">
                            Registrarse
                        </button>
                    </form>

                    <p class="text-center">
                        <span>¿Ya tienes una cuenta?</span>
                        <a href="{{ route('login') }}">
                            <span>Iniciar sesión</span>
                        </a>
                    </p>

                    <div class="divider my-6">
                        <div class="divider-text">o</div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-facebook me-1_5">
                            <i class="tf-icons ti ti-brand-facebook-filled"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-twitter me-1_5">
                            <i class="tf-icons ti ti-brand-twitter-filled"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-github me-1_5">
                            <i class="tf-icons ti ti-brand-github-filled"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-google-plus">
                            <i class="tf-icons ti ti-brand-google-filled"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Registro -->
        </div>
    </div>
@endsection
