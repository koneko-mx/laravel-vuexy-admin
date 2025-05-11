<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Settings;

use Illuminate\Support\Facades\Cache;
use Koneko\VuexyAdmin\Application\Events\Settings\SettingChanged;

class SettingCacheListener
{
    public function handle(SettingChanged $event): void
    {
        $keyHash   = md5($event->key);
        /*
        $groupHash = md5($event->namespace);
        $suffix    = $event->userId !== null ? "u:{$event->userId}" : 'global';

        Cache::forget("koneko.admin.settings.setting:{$keyHash}:{$suffix}");
        Cache::forget("koneko.admin.settings.group:{$groupHash}:{$suffix}");
        */
    }
}
