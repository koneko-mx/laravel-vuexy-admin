@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes generales')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-5">
            @livewire('vuexy-admin::app-description-card')
            @livewire('vuexy-admin::app-favicon-card')
        </div>
        <div class="col-lg-4">
            @livewire('vuexy-admin::logo-on-light-bg-card')
            @livewire('vuexy-admin::logo-on-dark-bg-card')
        </div>
    </div>
@endsection
