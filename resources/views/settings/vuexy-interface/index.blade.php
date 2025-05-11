@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes de la interfaz Vuexy')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    @livewire('vuexy-admin::vuexy-interface-index')
@endsection
