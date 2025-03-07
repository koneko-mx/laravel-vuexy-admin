@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Recuperar Contraseña')

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
                <!-- Recuperar Contraseña -->
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

                        <h4 class="mb-1">¿Olvidaste tu Contraseña? 🔒</h4>
                        <p class="mb-6">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña</p>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="mb-6">
                            @csrf
                            <div class="mb-6 fv-row">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Ingresa tu correo electrónico"
                                       required
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary d-grid w-100">
                                Enviar Enlace de Restablecimiento
                            </button>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="d-flex justify-content-center">
                                <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
                                Volver al Inicio de Sesión
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Recuperar Contraseña -->
            </div>
        </div>
    </div>
@endsection
