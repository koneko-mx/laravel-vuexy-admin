<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Enums\Notifications;

enum NotificationChannel: string
{
    case Toast  = 'toast';
    case Push   = 'push';
    case WebSocket = 'websocket';
    case Email  = 'email';
    case InApp  = 'inapp';
}
