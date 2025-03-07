@php
    $customizerHidden = 'customizer-hide';
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
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">

                <!-- Tarjeta de Registro -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-logo demo">
                                    <img src="{{ asset('storage/' . $_admin['image_logo']['small']) }}" alt="{{ $_admin['app_name'] }}" />
                                </span>
                                <span class="app-brand-text demo text-heading fw-bold">{{ $_admin['app_name'] }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Empieza tu aventura 🚀</h4>
                        <p class="mb-6">¡Gestiona tu aplicación de manera sencilla y divertida!</p>

                        <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-6 fv-row">
                                <label for="name" class="form-label">Nombre de usuario</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa tu nombre de usuario" autofocus required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-6 fv-row">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-6 fv-row form-password-toggle">
                                <label class="form-label" for="password">Contraseña</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-6 fv-row form-password-toggle">
                                <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>

                            <div class="fv-row">
                                <div class="my-8 fv-row">
                                <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required>
                                    <label class="form-check-label" for="terms-conditions">
                                        Acepto la <a href="javascript:void(0);">política de privacidad y términos</a>
                                    </label>
                                    @error('terms')
                                        <span class="invalid-feedback" role="alert">
                                            <span class="fw-medium">{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary d-grid w-100">
                                Registrarse
                            </button>
                        </form>

                        <p class="text-center">
                            <span>¿Ya tienes una cuenta?</span>
                            <a href="{{ route('login') }}">
                                <span>Inicia sesión</span>
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
                <!-- /Tarjeta de Registro -->
            </div>
        </div>
    </div>
@endsection
