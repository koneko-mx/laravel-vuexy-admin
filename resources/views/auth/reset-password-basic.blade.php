@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Restablecer Contraseña')

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
                <!-- Restablecer Contraseña -->
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
                        <h4 class="mb-1">Restablecer Contraseña 🔒</h4>
                        <p class="mb-6"><span class="fw-medium">Tu nueva contraseña debe ser diferente de las contraseñas utilizadas anteriormente</span></p>

                        <form method="POST" action="{{ route('password.update') }}" class="mb-6">
                            @csrf

                            {{-- Token de restablecimiento de contraseña --}}
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="mb-6 fv-row form-password-toggle">
                                <label class="form-label" for="email">Correo Electrónico</label>
                                <input type="email"
                                            id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email"
                                            value="{{ old('email', $request->email) }}"
                                            required
                                            autofocus
                                            readonly>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-6 fv-row form-password-toggle">
                                <label class="form-label" for="password">Nueva Contraseña</label>
                                <div class="input-group input-group-merge">
                                    <input type="password"
                                                id="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required
                                                autocomplete="new-password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-6 fv-row form-password-toggle">
                                <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                                <div class="input-group input-group-merge">
                                    <input type="password"
                                                id="password_confirmation"
                                                class="form-control"
                                                name="password_confirmation"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                required
                                                autocomplete="new-password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary d-grid w-100 mb-6">
                                Establecer Nueva Contraseña
                            </button>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="d-flex justify-content-center align-items-center">
                                    <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
                                    Volver al Inicio de Sesión
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Restablecer Contraseña -->
            </div>
        </div>
    </div>
@endsection
