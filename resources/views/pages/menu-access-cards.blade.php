@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', $title?? 'Dashboard')

@section('vendor-style')
    @vite('vendor/koneko/laravel-vuexy-admin/resources/scss/pages/quick-access-card.scss')
@endsection

@section('content')
    @livewire('vuexy-admin::menu-access-cards', ['slug' => $slug])
@endsection
