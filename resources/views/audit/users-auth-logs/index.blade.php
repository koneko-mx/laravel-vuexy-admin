@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Registros de accesos al sistema')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/bootstrap-table/bootstrap-table.scss',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/fonts/bootstrap-icons.scss',
    ])
@endsection

@push('page-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/bootstrap-table/bootstrapTableManager.js',
    ])
@endpush

@section('content')
    @livewire('vuexy-admin::auth-users-logs-table')
@endsection
