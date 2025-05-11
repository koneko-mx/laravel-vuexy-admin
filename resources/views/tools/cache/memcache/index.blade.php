@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Memcache')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/cache-manager-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if($configCache['memcachedInUse'])
                @livewire('vuexy-admin::memcached-stats-card')
            @else
                <div class="mb-6">
                    <div class="alert alert-danger">
                        <i class="flaticon-warning me-2"></i>
                        <span>No se ha encontrado un servidor de Memcache configurado.</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
