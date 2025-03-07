@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Two Steps Verifications Basic - Pages')

@section('vendor-style')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@push('page-style')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/cleavejs/cleave.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@push('page-script')
@vite([
  'vendor/koneko/laravel-vuexy-admin/resources/js/auth/pages-auth.js',
  'vendor/koneko/laravel-vuexy-admin/resources/js/auth/pages-auth-two-steps.js'
])
@endpush

@section('content')
<div class="authentication-wrapper authentication-basic px-6">
  <div class="authentication-inner py-6">
    <!--  Two Steps Verification -->
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
        <h4 class="mb-1">Two Step Verification 💬</h4>
        <p class="text-start mb-6">
          We sent a verification code to your mobile. Enter the code from the mobile in the field below.
          <span class="fw-medium d-block mt-1 text-heading">******1234</span>
        </p>
        <p class="mb-0">Type your 6 digit security code</p>
        <form id="twoStepsForm" action="{{url('/')}}" method="GET">
          <div class="mb-6">
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" autofocus>
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1">
            </div>
            <!-- Create a hidden field which is combined by 3 fields above -->
            <input type="hidden" name="otp" />
          </div>
          <button class="btn btn-primary d-grid w-100 mb-6">
            Verify my account
          </button>
          <div class="text-center">Didn't get the code?
            <a href="javascript:void(0);">
              Resend
            </a>
          </div>
        </form>
      </div>
    </div>
    <!-- / Two Steps Verification -->
  </div>
</div>
@endsection
