@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Roles de usuarios')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js',
    ])
@endsection

@section('content')
    @livewire('role-card')
@endsection
