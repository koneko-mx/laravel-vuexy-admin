@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Cache de Laravel')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/cache-manager-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            @livewire('vuexy-admin::cache-functions-card')
        </div>
    </div>
@endsection
