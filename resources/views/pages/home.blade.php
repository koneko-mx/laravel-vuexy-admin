@php
    $configData = Helper::appClasses();
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('content')
    @livewire('vuexy-admin::quick-access-widget');
@endsection
