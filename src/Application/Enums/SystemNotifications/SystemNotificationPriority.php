<?php


declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\SystemNotifications;

enum SystemNotificationPriority: string
{
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';
}