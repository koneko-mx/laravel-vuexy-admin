@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Perfil de usuario')

@section('content')
<?php /*
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
    <div class="mb-6">
        @livewire('vuexy-admin::update-profile-information-form')
    </div>
    @endif

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="mb-6">
        @livewire('profile.update-password-form')
        </div>
    @endif

    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
    <div class="mb-6">
        @livewire('profile.two-factor-authentication-form')
    </div>
    @endif

    <div class="mb-6">
        @livewire('profile.logout-other-browser-sessions-form')
    </div>

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        @livewire('profile.delete-user-form')
    @endif
    */ ?>
@endsection
