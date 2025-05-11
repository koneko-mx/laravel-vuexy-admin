@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Sesiones de usuarios Laravel')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/cache-manager-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4">
            @livewire('vuexy-admin::session-stats-card')
        </div>
    </div>
@endsection
