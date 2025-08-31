@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes generales')

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/forms/formCustomListener.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/livewire/registerLivewireHookOnce.js',
        'vendor/koneko/laravel-vuexy-admin/resources/assets/js/notifications/LivewireNotification.js',
    ])
@endsection

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

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.AppDescriptionSettingsForm = new formCustomListener({
                formSelector: '#app-description-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
            });

            registerLivewireHookOnce('morphed', 'vuexy-admin::app-description-card', (component) => {
                AppDescriptionSettingsForm.reloadValidation();
            });
        });
    </script>
@endpush
