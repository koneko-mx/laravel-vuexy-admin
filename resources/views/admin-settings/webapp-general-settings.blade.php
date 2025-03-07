@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes generales')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- App Settings Card -->
            <div class="mb-4">
                @livewire('application-settings')
            </div>
        </div>
        <div class="col-lg-4">
            <!-- General Settings Card -->
            <div class="mb-4">
                @livewire('general-settings')
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Interface Settings Card -->
            <div class="mb-4">
                @livewire('interface-settings')
            </div>
        </div>
    </div>
@endsection
