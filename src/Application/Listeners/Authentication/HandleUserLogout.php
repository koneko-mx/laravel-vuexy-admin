<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Authentication;

use Illuminate\Auth\Events\Logout;
use Koneko\VuexyAdmin\Application\UX\Navbar\VuexySearchBarBuilder;
use Koneko\VuexyAdmin\Application\UX\Navbar\VuexyQuicklinksBuilder;
use Koneko\VuexyAdmin\Application\UX\Menu\VuexyMenuFormatter;
use Koneko\VuexyAdmin\Application\UX\Notifications\VuexyNotificationsBuilder;
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
        VuexySearchBarBuilder::forgetCacheForUser($userId);
        VuexyQuicklinksBuilder::clearCacheForUser($userId);
        VuexyNotificationsBuilder::clearCacheForUser($userId);
    }
}
