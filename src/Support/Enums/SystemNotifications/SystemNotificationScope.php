<?php

namespace Koneko\VuexyAdmin\Support\Enums\SystemNotifications;

enum SystemNotificationScope: string
{
    case Admin    = 'admin';
    case Frontend = 'frontend';
    case Both     = 'both';
}