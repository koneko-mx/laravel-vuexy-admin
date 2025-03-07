@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Permisos del sistema')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js',
    ])
@endsection

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/roles-scripts.js/permissions-scripts.js')
@endpush


@section('content')
    @livewire('permissions-index')
@endsection
