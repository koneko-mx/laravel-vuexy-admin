@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Verify Email Basic - Pages')

@push('page-style')
<!-- Page -->
@vite('vendor/koneko/laravel-vuexy-admin/resources/scss/pages/page-auth.scss')
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic px-6">
  <div class="authentication-inner py-6">
    <!-- Verify Email -->
    <div class="card">
      <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-6">
          <a href="{{url('/')}}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="{{ asset('storage/' . $_admin['image_logo']['small']) }}" alt="{{ $_admin['app_name'] }}" />
            </span>
            <span class="app-brand-text demo text-heading fw-bold">{{ config('koneko.templateName') }}</span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-1">Verify your email ✉️</h4>
        <p class="text-start mb-0">
          Account activation link sent to your email address: hello@example.com Please follow the link inside to continue.
        </p>
        <a class="btn btn-primary w-100 my-6" href="{{url('/')}}">
          Skip for now
        </a>
        <p class="text-center mb-0">Didn't get the mail?
          <a href="javascript:void(0);">
            Resend
          </a>
        </p>
      </div>
    </div>
    <!-- /Verify Email -->
  </div>
</div>
@endsection
