@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Configuraciones de cuenta | ' . $user->name)

@section('content')
    @livewire('vuexy-admin::update-profile-information-form')
    @livewire('vuexy-admin::update-password-form')
@endsection
