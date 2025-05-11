@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Editar Usuario: ' . $user->name)

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/select2/select2.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/select2/select2.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/select2/es.js',
        //'vendor/koneko/laravel-vuexy-admin/resources/assets/js/notifications/LivewireNotification.js',
        'vendor/koneko/laravel-vuexy-contacts/resources/assets/js/addresses/AddressFormHandler.js',
    ])
@endsection

@section('content')
    @livewire('vuexy-admin::user-form', ['mode' => 'edit', 'id' => $user->id])
@endsection
