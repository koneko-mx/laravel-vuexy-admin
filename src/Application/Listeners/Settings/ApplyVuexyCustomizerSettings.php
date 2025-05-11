<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Settings;

use Koneko\VuexyAdmin\Application\Events\Settings\VuexyCustomizerSettingsUpdated;
use Koneko\VuexyAdmin\Application\Cache\VuexyVarsBuilderService;

class ApplyVuexyCustomizerSettings
{
    public function handle(VuexyCustomizerSettingsUpdated $event): void
    {
        foreach ($event->settings as $key => $value) {
            settings()->self('vuexy')->set($key, $value);
        }

        VuexyVarsBuilderService::clearCache();
    }
}
