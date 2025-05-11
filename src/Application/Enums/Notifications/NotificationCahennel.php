<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\Notifications;

enum NotificationCahennel: string
{
    case Toast  = 'toast';
    case Push   = 'push';
    case WebSocket = 'websocket';
    case Email  = 'email';
    case InApp  = 'inapp';
}
