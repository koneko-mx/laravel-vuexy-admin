<?php

namespace Koneko\VuexyAdmin\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Services\VuexyAdminService;

class ClearUserCache
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Logout $event)
    {
        if ($event->user) {
            VuexyAdminService::clearUserMenuCache();
            VuexyAdminService::clearSearchMenuCache();
            VuexyAdminService::clearQuickLinksCache();
            VuexyAdminService::clearNotificationsCache();
        }
    }
}
