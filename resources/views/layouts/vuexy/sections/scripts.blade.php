<!-- BEGIN: Vendor JS-->
@vite([
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/jquery/jquery.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/popper/popper.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/js/bootstrap.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/node-waves/node-waves.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/hammer/hammer.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/typeahead-js/typeahead.js',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/js/menu.js',
])

@yield('vendor-script')
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
@vite('vendor/koneko/laravel-vuexy-admin/resources/assets/js/main.js')
<!-- END: Theme JS-->

@vite('vendor/koneko/laravel-vuexy-admin/resources/js/app.js')

<!-- BEGIN: Page JS-->
@stack('page-script')
<!-- END: Page JS-->
