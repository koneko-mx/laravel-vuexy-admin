@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Variables de entorno')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/bootstrap-table/bootstrap-table.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/fonts/bootstrap-icons.scss',
    ])
@endsection

@push('page-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/bootstrap-table/bootstrapTableManager.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/forms/formConvasHelper.js',
    ])
@endpush

@section('content')
    @livewire('vuexy-admin::environment-vars-table')
@endsection
