<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers\Concerns;

use Illuminate\Support\Facades\URL;

trait EnforcesHttps
{
    protected function enforceHttps(): void
    {
        if (config_m()->get('security.https.force', false) ||
            request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
            app('request')->server->set('HTTPS', 'on');
        }
    }
}
