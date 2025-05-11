<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait RegistersTrustedProxies
{
    protected function registersTrustedProxies(): void
    {
        $trust_proxy     = config('koneko.admin.security.trust_proxy', false);
        $trust_proxy_ips = config('koneko.admin.security.trust_proxy_ips', '*');
        $force_https     = config('koneko.admin.security.force_https', false);

        if ($trust_proxy) {
            Request::setTrustedProxies(
                explode(',', $trust_proxy_ips), // admite múltiples IPs separadas por coma
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_PREFIX
            );
        }

        if ($force_https || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
            app('request')->server->set('HTTPS', 'on');
        }
    }
}
