<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Route;

    $menuFixed = ($configData['layout'] === 'vertical') ? ($menuFixed ?? '') : (($configData['layout'] === 'front') ? '' : $configData['headerType']);
    $navbarType = ($configData['layout'] === 'vertical') ? ($configData['navbarType'] ?? '') : (($configData['layout'] === 'front') ? 'layout-navbar-fixed': '');
    $isFront = ($isFront ?? '') == true ? 'Front' : '';
    $contentLayout = (isset($container) ? (($container === 'container-xxl') ? "layout-compact" : "layout-wide") : "");
@endphp
<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
    class="{{ $configData['style'] }}-style {{($contentLayout ?? '')}} {{ ($navbarType ?? '') }} {{ ($menuFixed ?? '') }} {{ $menuCollapsed ?? '' }} {{ $menuFlipped ?? '' }} {{ $menuOffcanvas ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
    dir="{{ $configData['textDirection'] }}"
    data-theme="{{ $configData['theme'] }}"
    data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') . '/' }}"
    data-route="{{ Route::currentRouteName() }}"
    data-framework="laravel"
    data-template="{{ $configData['layout'] . '-menu-' . $configData['themeOpt'] . '-' . $configData['styleOpt'] }}"
    data-style="{{$configData['styleOptVal']}}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@hasSection('title') @yield('title') | @endif {{ $_admin['title'] }}</title>
    <meta name="description" content="{{ $_admin['description'] }}" />

    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons-->
    <link rel="icon" href="{{ asset('storage/' . $_admin['favicon']['16x16']) }}" type="image/x-icon">
    <link rel="icon" sizes="192x192" href="{{ asset('storage/' . $_admin['favicon']['192x192']) }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="76x76" href="{{ asset('storage/' . $_admin['favicon']['76x76']) }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="120x120" href="{{ asset('storage/' . $_admin['favicon']['120x120']) }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="152x152" href="{{ asset('storage/' . $_admin['favicon']['152x152']) }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="180x180" href="{{ asset('storage/' . $_admin['favicon']['180x180']) }}">

    <!-- Include Styles -->
    <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
    @include('vuexy-admin::layouts.vuexy.sections.styles' . $isFront)

    <!-- Include Scripts for customizer, helper, analytics, config -->
    <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
    @include('vuexy-admin::layouts.vuexy.sections.scriptsIncludes' . $isFront)

    <!-- Notifications -->
    @include('vuexy-admin::layouts.vuexy.sections.notifySession')
</head>
<body>
    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->

    <!-- Include Scripts -->
    @include('vuexy-admin::layouts.vuexy.sections.scripts' . $isFront)
</body>
</html>
