@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Usuario | ' . $user->name)

@section('content')
    @livewire('vuexy-admin::user-details-viewer-index', ['user' => $user])
@endsection
