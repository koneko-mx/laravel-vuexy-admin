@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Visor de usuario')

@section('content')
    @livewire('vuexy-admin::user-details-viewer-index', ['user' => $user])
@endsection
