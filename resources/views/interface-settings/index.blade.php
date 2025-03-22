@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes de la interfaz')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    @livewire('vuexy-admin::interface-settings')
@endsection
