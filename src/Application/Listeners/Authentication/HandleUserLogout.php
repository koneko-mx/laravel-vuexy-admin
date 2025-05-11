<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Authentication;

use Illuminate\Auth\Events\Logout;
use Koneko\VuexyAdmin\Application\UX\Navbar\{VuexySearchBarBuilderService,VuexyQuicklinksBuilderService};
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Application\UX\Notifications\VuexyNotificationsBuilderService;
use Koneko\VuexyAdmin\Models\UserLogin;

class HandleUserLogout
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            $userId = $event->user->id;

            // Actualiza el registro de login más reciente que no tenga logout registrado
            UserLogin::closeLastActiveLoginForUser($userId);

            // Limpia la cache de entorno de usuario
            $this->clearUserCaches($userId);
        }
    }

    private function clearUserCaches(int $userId): void
    {
        VuexyMenuFormatter::forgetCacheForUser($userId);
        VuexySearchBarBuilderService::forgetCacheForUser($userId);
        VuexyQuicklinksBuilderService::clearCacheForUser($userId);
        VuexyNotificationsBuilderService::clearCacheForUser($userId);
    }
}
