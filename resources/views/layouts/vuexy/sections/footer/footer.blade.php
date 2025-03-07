@php
    $containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                © <script>document.write(new Date().getFullYear())</script>, Hecho con ❤️ por  <a href="{{ (!empty(config('koneko.creatorUrl')) ? config('koneko.creatorUrl') : '') }}" target="_blank" class="footer-link">{{ (!empty(config('koneko.creatorName')) ? config('koneko.creatorName') : '') }}</a>
            </div>
            <div class="d-none d-lg-inline-block">
                @if (config('koneko.licenseUrl'))
                    <a href="{{ config('koneko.licenseUrl') }}" class="footer-link me-4" target="_blank">Licencia</a>
                @endif
                @if (config('koneko.supportUrl'))
                    <a href="{{ config('koneko.supportUrl') }}" target="_blank" class="footer-link d-none d-sm-inline-block">Soporte</a>
                @endif
            </div>
        </div>
    </div>
</footer>
<!--/ Footer-->
