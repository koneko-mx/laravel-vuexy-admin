@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes de caché')

@push('page-script')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/js/pages/cache-manager-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="mb-6">
                @livewire('vuexy-admin::cache-stats')
            </div>
            <div class="mb-6">
                @livewire('vuexy-admin::session-stats')
            </div>
        </div>
        <div class="col-md-8">
            <div class="mb-6">
                @livewire('vuexy-admin::cache-functions')
            </div>
            <div class="row">
                @if($configCache['redisInUse'])
                    <div class="col-md-6">
                        <div class="mb-6">
                            @livewire('vuexy-admin::redis-stats')
                        </div>
                    </div>
                @endif
                @if($configCache['memcachedInUse'])
                    <div class="col-md-6">
                        <div class="mb-6">
                            @livewire('vuexy-admin::memcached-stats')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
