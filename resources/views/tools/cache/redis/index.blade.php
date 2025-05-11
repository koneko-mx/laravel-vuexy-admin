@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Redis Cache')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/cache-manager-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if($configCache['redisInUse'])
                @livewire('vuexy-admin::redis-stats-card')
            @else
                <div class="mb-6">
                    <div class="alert alert-danger">
                        <i class="flaticon-warning me-2"></i>
                        <span>No se ha encontrado un servidor de caché Redis configurado.</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
