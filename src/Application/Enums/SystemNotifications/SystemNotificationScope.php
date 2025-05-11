<?php

namespace Koneko\VuexyAdmin\Application\Enums\SystemNotifications;

enum SystemNotificationScope: string
{
    case Admin    = 'admin';
    case Frontend = 'frontend';
    case Both     = 'both';
}