@php
    use Laravel\Fortify\Features;

    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Iniciar sesión')

@section('vendor-style')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss')
@endsection

@push('page-style')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/scss/pages/page-auth.scss')
@endpush

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js'
    ])
@endsection

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/auth/pages-auth.js')
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
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('vendor/vuexy-admin/img/illustrations/auth-login-illustration-'.$configData['style'].'.png') }}" alt="auth-login-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-login-illustration-light.png" data-app-dark-img="illustrations/auth-login-illustration-dark.png">
                    <img src="{{ asset('vendor/vuexy-admin/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">¡Bienvenido a {{ $_admin['app_name'] }}! 👋</h4>
                    <p class="mb-6">Inicie sesión en su cuenta y comience la aventura</p>

                    <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-6 fv-row">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Ingrese el correo electrónico" autofocus value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-6 fv-row form-password-toggle">
                            <label class="form-label" for="password">Contraseña</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <span class="fw-medium">{{ $message }}</span>
                                </span>
                            @enderror
                        </div>
                        <div class="my-8">
                            <div class="d-flex justify-content-between">
                                <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                    <label class="form-check-label" for="remember-me">
                                        Recuérdame
                                    </label>
                                </div>
                                @if (Features::enabled(Features::resetPasswords()))
                                    <a href="{{ route('password.request') }}">
                                        <p class="mb-0">¿Olvidaste tu contraseña?</p>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100">
                            Iniciar sesión
                        </button>
                    </form>

                    <p class="text-center">
                        @if (Features::enabled(Features::registration()))
                            <span>¿Nuevo en nuestra plataforma?</span>
                            <a href="{{ route('register') }}">
                                <span>Crea una cuenta</span>
                            </a>
                        @endif
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
            <!-- /Login -->
        </div>
    </div>
@endsection
