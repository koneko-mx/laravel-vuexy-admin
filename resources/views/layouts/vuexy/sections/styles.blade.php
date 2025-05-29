<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

@vite([
    //'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/fonts/fontawesome.scss',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/node-waves/node-waves.scss',
])

<!-- Core CSS -->
@vite([
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData['rtlSupport'].'/core' .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.scss',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData['rtlSupport'].'/' .$configData['theme'] .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.scss',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/css/demo.css',
    'vendor/koneko/laravel-vuexy-admin/resources/scss/app.scss',
])

<!-- Vendor Styles -->
@vite([
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/typeahead-js/typeahead.scss',
    'vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/libs/toastr/toastr.scss',
])

@yield('vendor-style')

<!-- Page Styles -->
@stack('page-style')
