<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers\Concerns;

use Illuminate\Http\Request;

trait RegistersTrustedProxies
{
    protected function registerTrustedProxies(): void
    {
        $proxies = config_m()->get('security.proxies', []);

        if ($proxies['enabled'] ?? false) {
            Request::setTrustedProxies(
                explode(',', $proxies['ips'] ?? '*'),
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_PREFIX
            );
        }
    }
}
