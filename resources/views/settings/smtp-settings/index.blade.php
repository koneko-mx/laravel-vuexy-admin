@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Configuracion SMTP')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss'
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
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/smtp-settings-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="mb-4">
                @livewire('vuexy-admin::smtp-settings-card')
            </div>
        </div>
    </div>
@endsection
