@php
    $menuCollapsed = ($configData['menuCollapsed'] === 'layout-menu-collapsed') ? json_encode(true) : false;
@endphp

<!-- Laravel Helper Functions -->
@vite('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/js/helpers.js')

@if ($configData['hasCustomizer'])
    <!--! Template customizer & Theme config files -->
    @vite('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/js/template-customizer.js')
@endif

<!-- Config File -->
@vite('vendor/koneko/laravel-vuexy-admin/resources/assets/js/config.js')

<!-- Notificaciones Vuexy (alerts tipo Bootstrap) -->
@if (session('vuexy_notification'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notification = @json(session('vuexy_notification'));

            if (window.vuexyNotify) {
                window.vuexyNotify.flash(notification);

            } else {
                console.warn('⚠️ VuexyNotify no encontrado');
            }
        });
    </script>
@endif

<!-- Notificaciones Toastr (tipo push) -->
@if (session('vuexy_toastr'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastrData = @json(session('vuexy_toastr'));

            if (window.toastr) {
                toastr.options.timeOut = toastrData.delay || 5000;
                toastr.options.positionClass = "toast-top-right";
                toastr[toastrData.type](toastrData.message);

            } else {
                console.warn('⚠️ Toastr no encontrado');
            }
        });
    </script>
@endif

@if ($configData['hasCustomizer'])
    <script type="module">
        window.templateCustomizer = new TemplateCustomizer({
            cssPath: '',
            themesPath: '',
            defaultStyle: "{{ $configData['styleOpt'] }}",
            defaultShowDropdownOnHover: "{{ $configData['showDropdownOnHover'] }}", // true/false (for horizontal layout only)
            displayCustomizer: "{{ $configData['displayCustomizer'] }}",
            lang: '{{ app()->getLocale() }}',
            pathResolver: function(path) {
                var resolvedPaths = {
                    // Core stylesheets
                    @foreach (['core'] as $name)
                        '{{ $name }}.scss': '{{ Vite::asset('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'.scss') }}',
                        '{{ $name }}-dark.scss': '{{ Vite::asset('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'-dark.scss') }}',
                    @endforeach

                    // Themes
                    @foreach (['default', 'bordered', 'semi-dark'] as $name)
                        'theme-{{ $name }}.scss': '{{ Vite::asset('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'.scss') }}',
                        'theme-{{ $name }}-dark.scss': '{{ Vite::asset('vendor/koneko/laravel-vuexy-admin/resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'-dark.scss') }}',
                    @endforeach
                };

                return resolvedPaths[path] || path;
            },
            'controls': <?php echo json_encode($configData['customizerControls']); ?>,
        });
    </script>
@endif
